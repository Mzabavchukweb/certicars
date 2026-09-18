<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ErrorLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'level', 'source', 'message', 'context', 'user_id', 'car_id',
        'url', 'method', 'ip', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'context'    => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Zapis do rejestru. Nigdy nie rzuca — rejestr błędów nie może być
     * kolejnym źródłem błędów (np. gdy baza jest niedostępna).
     */
    public static function record(string $source, string $message, array $context = [], string $level = 'error', ?int $carId = null): void
    {
        try {
            $req = app()->bound('request') ? request() : null;
            static::create([
                'level'      => $level,
                'source'     => mb_substr($source, 0, 64),
                'message'    => mb_substr($message, 0, 1000),
                'context'    => static::trimContext($context),
                'user_id'    => optional($req?->user())->id,
                'car_id'     => $carId,
                'url'        => $req ? mb_substr($req->fullUrl(), 0, 500) : null,
                'method'     => $req?->method(),
                'ip'         => $req?->ip(),
                'user_agent' => $req ? mb_substr((string) $req->userAgent(), 0, 300) : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('error_log.write_failed', ['err' => $e->getMessage(), 'source' => $source, 'message' => $message]);
        }
    }

    public static function fromException(\Throwable $e): void
    {
        $ctx = [
            'exception' => get_class($e),
            'file'      => basename($e->getFile()) . ':' . $e->getLine(),
            'trace'     => collect(explode("\n", $e->getTraceAsString()))->take(12)->implode("\n"),
        ];
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            $ctx['errors'] = $e->errors();
            static::record('validation', 'Walidacja nie przeszła: ' . implode(' | ', array_map(fn($m) => $m[0] ?? '', $e->errors())), $ctx, 'warning');
            return;
        }
        static::record('exception', $e->getMessage() ?: get_class($e), $ctx);
    }

    /** Kontekst przycięty do rozsądnego rozmiaru (bez haseł/tokenów). */
    private static function trimContext(array $context): array
    {
        $out = [];
        foreach ($context as $k => $v) {
            if (in_array(strtolower((string) $k), ['password', 'password_confirmation', '_token', 'token'], true)) continue;
            if (is_string($v) && mb_strlen($v) > 4000) $v = mb_substr($v, 0, 4000) . '…';
            $out[$k] = $v;
        }
        $json = json_encode($out);
        if ($json !== false && strlen($json) > 60000) {
            $out = ['_truncated' => true, 'summary' => mb_substr($json, 0, 4000)];
        }
        return $out;
    }
}
