<?php

namespace App\Support;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Ochrona formularzy przed spamem — bez zewnętrznych usług, warstwowo:
 *
 *  1. honeypot            — ukryte pole `website`, wypełniane tylko przez boty
 *  2. pułapka czasowa     — podpisany znacznik `_ts`; wysyłka < 3 s = bot
 *  3. limit na IP         — więcej niż kilka wiadomości na godzinę = spam
 *  4. powtórki            — ta sama treść wysłana ponownie = spam
 *  5. ocena treści        — linki, obcy alfabet, angielskie zwroty nagród,
 *                           KRZYKI, brak polskich znaków przy długim tekście,
 *                           nazwisko sklejone jak "Williamhaupt" itd.
 *  6. Cloudflare Turnstile — jeśli w konfiguracji są klucze (opcjonalne)
 *
 * Wynik: liczba punktów + powody. Od SPAM_THRESHOLD wiadomość trafia do
 * kwarantanny (zakładka Spam w adminie), a nie do skrzynki. Nic nie jest
 * kasowane automatycznie — fałszywy alarm da się przywrócić jednym kliknięciem.
 */
class SpamGuard
{
    public const SPAM_THRESHOLD = 5;
    public const MIN_SECONDS    = 3;
    public const MAX_AGE        = 43200; // 12 h — starszy formularz = podejrzany

    /** Słowa/zwroty z wiadomości, których żaden klient salonu nie napisze. */
    private const BAD_PHRASES = [
        'lamborghini avent', 'playstation 5', 'you are a quick time', 'fastest time from winning',
        'winning', 'casino', 'kasyno online', 'bitcoin', 'crypto', 'forex', 'binary option',
        'viagra', 'cialis', 'porn', 'sex dating', 'escort', 'loan offer', 'payday',
        'seo services', 'backlink', 'guest post', 'link building', 'rank your site',
        'increase your traffic', 'web design services', 'telegram', 'whatsapp me',
        'make money', 'earn money', 'investment opportunity', 'nft', 'airdrop',
        'aloha, makemake', 'kumuku', 'unsubscribe', 'click here', 'act now',
    ];

    /** Zwroty typowe dla ofert „pozycjonowania” i masowych wysyłek po polsku. */
    private const BAD_PHRASES_PL = [
        'oferta pozycjonowania', 'zwiększymy sprzedaż', 'darmowy audyt', 'kredyt bez bik',
        'chwilówka', 'zarabiaj', 'inwestycja w kryptowaluty', 'baza firm', 'wysyłka mailingu',
    ];

    /**
     * @return array{score:int, reasons:array<int,string>, spam:bool}
     */
    public function inspect(Request $request, string $name, ?string $email, ?string $phone, ?string $message, string $kind = 'contact'): array
    {
        $score = 0;
        $reasons = [];
        $add = function (int $points, string $why) use (&$score, &$reasons) {
            $score += $points;
            $reasons[] = $why;
        };

        // 1. honeypot
        if (trim((string) $request->input('website')) !== '') {
            $add(10, 'Wypełnione ukryte pole (honeypot)');
        }

        // 2. pułapka czasowa
        $ts = $this->readTimestamp($request->input('_ts'));
        if ($ts === null) {
            $add(3, 'Brak znacznika czasu formularza');
        } else {
            $age = time() - $ts;
            if ($age < self::MIN_SECONDS) $add(6, 'Formularz wysłany w ' . max(0, $age) . ' s — za szybko na człowieka');
            if ($age > self::MAX_AGE)     $add(3, 'Formularz otwarty ponad 12 h wcześniej');
        }

        // 3. limit na IP (poza throttle — ten liczy w minutach)
        $ip = (string) $request->ip();
        if ($ip !== '') {
            $key = 'spamguard:ip:' . md5($ip);
            $count = (int) Cache::get($key, 0);
            Cache::put($key, $count + 1, now()->addHour());
            if ($count >= 3) $add(5, 'Ten sam adres IP wysłał już ' . $count . ' wiadomości w ciągu godziny');
        }

        // 4. powtórki tej samej treści
        if ($message !== null && mb_strlen(trim($message)) > 12) {
            $hash = md5(mb_strtolower(preg_replace('/\s+/u', ' ', trim($message))));
            if (ContactMessage::where('body_hash', $hash)->where('created_at', '>=', now()->subDays(30))->exists()) {
                $add(6, 'Ta sama treść była już wysłana');
            }
        }

        // 5. ocena treści
        $content = $this->contentScore($name, $phone, $message);
        foreach ($content['reasons'] as $i => $why) {
            $add($content['points'][$i], $why);
        }

        // 6. Cloudflare Turnstile (jeśli włączone w konfiguracji)
        if ($this->turnstileEnabled()) {
            if (!$this->turnstilePasses($request)) {
                $add(10, 'Nie przeszedł weryfikacji Cloudflare Turnstile');
            }
        }

        return [
            'score'   => $score,
            'reasons' => $reasons,
            'spam'    => $score >= self::SPAM_THRESHOLD,
        ];
    }

    /**
     * Ocena samej treści — bez honeypota, znacznika czasu i licznika IP.
     * Używana i przy nowych wiadomościach, i przy przeglądaniu starych.
     *
     * @return array{points:array<int,int>, reasons:array<int,string>, score:int}
     */
    public function contentScore(?string $name, ?string $phone, ?string $message): array
    {
        $points = [];
        $reasons = [];
        $add = function (int $p, string $why) use (&$points, &$reasons) {
            $points[] = $p;
            $reasons[] = $why;
        };

        $text = trim(($name ?? '') . "\n" . ($message ?? ''));
        $lower = mb_strtolower($text);

        $links = preg_match_all('~(https?://|www\.|\b[a-z0-9-]+\.(?:ru|top|xyz|click|loan|work|buzz|online|site|shop)\b)~iu', $text);
        if ($links >= 1) $add(4, 'Wiadomość zawiera link' . ($links > 1 ? "i ({$links})" : ''));

        foreach (array_merge(self::BAD_PHRASES, self::BAD_PHRASES_PL) as $bad) {
            if (str_contains($lower, $bad)) { $add(5, 'Zwrot typowy dla spamu: „' . $bad . '”'); break; }
        }

        if (preg_match('/[\x{0400}-\x{04FF}\x{4E00}-\x{9FFF}\x{0600}-\x{06FF}]/u', $text)) {
            $add(5, 'Tekst w obcym alfabecie (cyrylica / chiński / arabski)');
        }

        if ($message && mb_strlen($message) >= 25) {
            $letters = preg_replace('/[^\p{L}]/u', '', $message);
            $upper = preg_replace('/[^\p{Lu}]/u', '', $message);
            if ($letters !== '' && mb_strlen($upper) / mb_strlen($letters) > 0.7) {
                $add(4, 'Wiadomość pisana SAMYMI WIELKIMI LITERAMI');
            }
            // Obcojęzyczna wiadomość sama w sobie nie jest spamem (zdarzają się
            // klienci z zagranicy) — to tylko drobna poszlaka.
            $hasPlChars = preg_match('/[ąćęłńóśźżĄĆĘŁŃÓŚŹŻ]/u', $message);
            $hasPlWords = preg_match('/\b(samoch|auto|cena|kontakt|dzie[ńn]|prosz|chcia|oferta|zapyta|dobry|witam|czy)/iu', $message);
            if (!$hasPlChars && !$hasPlWords && mb_strlen($message) > 60) {
                $add(2, 'Długa wiadomość bez ani jednego polskiego słowa');
            }
        }

        // Nazwy typu "Williamhaupt", "Robertanype" — dwa sklejone imiona, bez spacji
        if ($name && !str_contains(trim($name), ' ') && mb_strlen($name) >= 9
            && preg_match('/^[A-Z][a-z]{8,}$/u', trim($name))
            && !preg_match('/[ąćęłńóśźż]/u', mb_strtolower($name))) {
            $add(2, 'Podejrzana nazwa nadawcy: „' . $name . '”');
        }

        // Telefon: same cyfry, 11 znaków, zaczyna się od 8 (typowe dla botów)
        if ($phone && preg_match('/^8\d{10}$/', preg_replace('/\D/', '', $phone))) {
            $add(3, 'Numer telefonu w formacie używanym przez boty');
        }

        return ['points' => $points, 'reasons' => $reasons, 'score' => array_sum($points)];
    }

    /** Podpisany znacznik czasu do wstawienia w formularz. */    /** Podpisany znacznik czasu do wstawienia w formularz. */
    public static function timestampField(): string
    {
        $t = (string) time();
        return $t . '.' . hash_hmac('sha256', $t, config('app.key'));
    }

    private function readTimestamp($value): ?int
    {
        if (!is_string($value) || !str_contains($value, '.')) return null;
        [$t, $sig] = explode('.', $value, 2);
        if (!ctype_digit($t)) return null;
        if (!hash_equals(hash_hmac('sha256', $t, config('app.key')), $sig)) return null;
        return (int) $t;
    }

    public function turnstileEnabled(): bool
    {
        return (bool) config('services.turnstile.secret') && (bool) config('services.turnstile.sitekey');
    }

    private function turnstilePasses(Request $request): bool
    {
        $token = (string) $request->input('cf-turnstile-response');
        if ($token === '') return false;
        try {
            $res = Http::asForm()->timeout(8)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret'   => config('services.turnstile.secret'),
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);
            return (bool) ($res->json('success') ?? false);
        } catch (\Throwable $e) {
            // Cloudflare niedostępne — nie blokujemy klientów, reszta warstw działa
            return true;
        }
    }

    public static function bodyHash(?string $message): ?string
    {
        if ($message === null || trim($message) === '') return null;
        return md5(mb_strtolower(preg_replace('/\s+/u', ' ', trim($message))));
    }

    /** Ocena treści już zapisanej (do przejrzenia starych wiadomości). */
    public function inspectStored(?string $name, ?string $email, ?string $phone, ?string $message): array
    {
        $c = $this->contentScore($name, $phone, $message);
        return [
            'score'   => $c['score'],
            'reasons' => $c['reasons'],
            'spam'    => $c['score'] >= self::SPAM_THRESHOLD,
        ];
    }
}
