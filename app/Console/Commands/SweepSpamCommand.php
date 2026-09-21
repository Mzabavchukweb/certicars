<?php

namespace App\Console\Commands;

use App\Models\ContactMessage;
use App\Support\SpamGuard;
use Illuminate\Console\Command;

/**
 * Przegląda wiadomości, które są już w bazie, i przenosi oczywisty spam do
 * kwarantanny. Nic nie kasuje — operator decyduje w panelu.
 */
class SweepSpamCommand extends Command
{
    protected $signature = 'spam:sweep {--dry : tylko pokaż, nic nie zmieniaj}';
    protected $description = 'Oznacza istniejące wiadomości ze spamem (zakładka Spam w adminie)';

    public function handle(SpamGuard $guard): int
    {
        $dry = (bool) $this->option('dry');
        $marked = 0;

        ContactMessage::where('is_spam', false)->orderBy('id')->chunk(200, function ($rows) use ($guard, $dry, &$marked) {
            foreach ($rows as $m) {
                $v = $guard->inspectStored($m->name, $m->email, $m->phone, $m->message);
                if (!$v['spam']) continue;
                $marked++;
                $this->line(sprintf('#%d %s — %d pkt: %s', $m->id, $m->name, $v['score'], $v['reasons'][0] ?? ''));
                if (!$dry) {
                    $m->forceFill([
                        'is_spam'      => true,
                        'spam_score'   => $v['score'],
                        'spam_reasons' => $v['reasons'],
                        'read_at'      => $m->read_at ?? now(),
                        'body_hash'    => $m->body_hash ?? SpamGuard::bodyHash($m->message),
                    ])->save();
                }
            }
        });

        $this->info($dry
            ? "Do oznaczenia jako spam: {$marked}. Uruchom bez --dry, żeby przenieść."
            : "Przeniesiono do spamu: {$marked}.");

        return self::SUCCESS;
    }
}
