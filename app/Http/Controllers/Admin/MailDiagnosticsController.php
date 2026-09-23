<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Diagnostyka poczty (Admin → Poczta).
 *
 * Odpowiada na dwa pytania, których nie da się sprawdzić z zewnątrz:
 *  1. Czy serwer strony potrafi WYSŁAĆ e-mail (ustawienia SMTP na Railway)?
 *  2. Czy skrzynka kontakt@certicars.pl PRZYJMUJE pocztę (test na porcie 25
 *     do serwera z rekordu MX — bez wysyłania treści, sam adres odbiorcy)?
 * Plus podgląd wpisów DNS: MX, SPF, DKIM, DMARC.
 */
class MailDiagnosticsController extends Controller
{
    private const DOMAIN  = 'certicars.pl';
    private const MAILBOX = 'kontakt@certicars.pl';

    public function index()
    {
        $mailer = config('mail.default');
        $conn   = config('mail.mailers.' . $mailer, []);

        $cfg = [
            'mailer'    => $mailer,
            'host'      => $conn['host'] ?? null,
            'port'      => $conn['port'] ?? null,
            'szyfrowanie' => $conn['scheme'] ?? ($conn['encryption'] ?? null),
            'login'     => $conn['username'] ?? null,
            'haslo'     => !empty($conn['password']) ? 'ustawione (' . strlen((string) $conn['password']) . ' znaków)' : 'BRAK',
            'nadawca'   => config('mail.from.address') . ' (' . config('mail.from.name') . ')',
        ];

        return view('admin.mail.index', [
            'cfg'      => $cfg,
            'dns'      => $this->dnsCheck(),
            'porty'    => $this->portCheck(),
            'przyjmuje'=> $this->inboundCheck(),
            'mailbox'  => self::MAILBOX,
        ]);
    }

    /** Wysyła prawdziwy testowy e-mail i pokazuje dokładny błąd, jeśli się nie uda. */
    public function send(Request $request)
    {
        $data = $request->validate(['to' => 'required|email']);
        try {
            Mail::raw(
                "To jest testowa wiadomość z panelu CertiCars.\n\n"
                . 'Wysłana: ' . now()->format('d.m.Y H:i:s') . "\n"
                . 'Serwer: ' . config('mail.default') . ' / ' . (config('mail.mailers.' . config('mail.default') . '.host') ?: '—') . "\n\n"
                . 'Jeśli ta wiadomość dotarła, wysyłka ze strony działa poprawnie.',
                function ($m) use ($data) {
                    $m->to($data['to'])->subject('CertiCars — test wysyłki poczty');
                }
            );
        } catch (\Throwable $e) {
            ErrorLog::record('mail.test', 'Test wysyłki nie powiódł się: ' . $e->getMessage(), [
                'to'     => $data['to'],
                'mailer' => config('mail.default'),
                'host'   => config('mail.mailers.' . config('mail.default') . '.host'),
            ]);
            return back()->with('error', 'Nie udało się wysłać: ' . $e->getMessage());
        }

        if (config('mail.default') === 'log') {
            return back()->with('warning', 'Uwaga: serwer ma ustawiony tryb „log” — wiadomość trafiła do pliku logu, a NIE do adresata. Trzeba uzupełnić dane SMTP.');
        }
        return back()->with('success', 'Wysłano wiadomość testową na ' . $data['to'] . '. Sprawdź skrzynkę (także folder Spam).');
    }

    /** MX / SPF / DKIM / DMARC — tak jak widzi je serwer. */
    private function dnsCheck(): array
    {
        $out = [];
        $mx = @dns_get_record(self::DOMAIN, DNS_MX) ?: [];
        usort($mx, fn($a, $b) => ($a['pri'] ?? 0) <=> ($b['pri'] ?? 0));
        $out['MX (gdzie trafia poczta)'] = $mx
            ? implode(', ', array_map(fn($r) => $r['target'] . ' (priorytet ' . $r['pri'] . ')', $mx))
            : 'BRAK — poczta przychodząca nie ma dokąd trafić';

        $txt = array_column(@dns_get_record(self::DOMAIN, DNS_TXT) ?: [], 'txt');
        $spf = array_values(array_filter($txt, fn($t) => str_starts_with($t, 'v=spf1')));
        $out['SPF (kto może wysyłać w imieniu domeny)'] = $spf ? implode(' | ', $spf) : 'BRAK';

        $dkim = @dns_get_record('dkim._domainkey.' . self::DOMAIN, DNS_TXT) ?: [];
        $out['DKIM (podpis wiadomości)'] = $dkim ? 'jest (selektor „dkim”)' : 'BRAK';

        $dmarc = array_column(@dns_get_record('_dmarc.' . self::DOMAIN, DNS_TXT) ?: [], 'txt');
        $out['DMARC'] = $dmarc ? implode(' | ', $dmarc) : 'BRAK';

        return $out;
    }

    /** Czy serwer strony w ogóle dosięga portów pocztowych. */
    private function portCheck(): array
    {
        $host = $this->primaryMx() ?: 'poczta2620527.home.pl';
        $res = [];
        foreach ([25 => 'odbiór poczty (SMTP)', 587 => 'wysyłka (submission)', 465 => 'wysyłka (SSL)', 993 => 'IMAP'] as $port => $opis) {
            $t0 = microtime(true);
            $fp = @fsockopen($host, $port, $errno, $errstr, 6);
            $ms = round((microtime(true) - $t0) * 1000);
            if ($fp) {
                stream_set_timeout($fp, 5);
                $banner = trim((string) fgets($fp, 512));
                fclose($fp);
                $res["{$host}:{$port} — {$opis}"] = ['ok' => true, 'info' => ($banner !== '' ? $banner : 'połączono') . " ({$ms} ms)"];
            } else {
                $res["{$host}:{$port} — {$opis}"] = ['ok' => false, 'info' => trim($errstr ?: 'brak połączenia') . " ({$ms} ms)"];
            }
        }
        return $res;
    }

    /**
     * Test odbioru: rozmowa z serwerem MX na porcie 25 do momentu „RCPT TO”.
     * Żadna wiadomość nie jest wysyłana — sprawdzamy tylko, czy serwer
     * przyjmuje adres kontakt@certicars.pl (kod 250 = skrzynka istnieje).
     */
    private function inboundCheck(): array
    {
        $host = $this->primaryMx();
        if (!$host) return ['ok' => false, 'kroki' => [], 'wniosek' => 'Brak rekordu MX — poczta przychodząca nie zadziała.'];

        $fp = @fsockopen($host, 25, $errno, $errstr, 8);
        if (!$fp) {
            return [
                'ok' => null,
                'kroki' => [],
                'wniosek' => "Nie udało się połączyć z {$host} na porcie 25 ({$errstr}). Najczęściej to blokada portu 25 po stronie hostingu — nie oznacza awarii poczty.",
            ];
        }
        stream_set_timeout($fp, 8);
        $kroki = [];
        $say = function (?string $cmd) use ($fp, &$kroki) {
            if ($cmd !== null) fwrite($fp, $cmd . "\r\n");
            $resp = '';
            while ($line = fgets($fp, 1024)) {
                $resp .= $line;
                if (strlen($line) < 4 || $line[3] !== '-') break;
            }
            $kroki[] = ['cmd' => $cmd ?? '(powitanie serwera)', 'resp' => trim($resp)];
            return trim($resp);
        };

        $say(null);
        $say('EHLO ' . self::DOMAIN);
        $say('MAIL FROM:<test@' . self::DOMAIN . '>');
        $rcpt = $say('RCPT TO:<' . self::MAILBOX . '>');
        $say('QUIT');
        fclose($fp);

        $ok = str_starts_with($rcpt, '250') || str_starts_with($rcpt, '251');
        return [
            'ok' => $ok,
            'kroki' => $kroki,
            'wniosek' => $ok
                ? 'Serwer przyjmuje pocztę na adres ' . self::MAILBOX . ' — odbiór działa.'
                : 'Serwer ODRZUCIŁ adres ' . self::MAILBOX . ': ' . $rcpt . ' — najczęściej skrzynka nie istnieje albo domena nie jest przypisana do serwera pocztowego.',
        ];
    }

    private function primaryMx(): ?string
    {
        $mx = @dns_get_record(self::DOMAIN, DNS_MX) ?: [];
        if (!$mx) return null;
        usort($mx, fn($a, $b) => ($a['pri'] ?? 0) <=> ($b['pri'] ?? 0));
        return $mx[0]['target'] ?? null;
    }
}
