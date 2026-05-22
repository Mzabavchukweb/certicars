<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    protected $fillable = [
        'car_id', 'type', 'name', 'phone', 'email',
        'message', 'car_title', 'ip', 'user_agent', 'read_at',
        'form_source', 'source_page', 'referrer',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
        'admin_email_sent_at', 'admin_email_failed_at', 'admin_email_error',
        'buyer_confirmation_sent_at', 'buyer_confirmation_failed_at', 'buyer_confirmation_error',
    ];

    protected $casts = [
        'read_at'                    => 'datetime',
        'admin_email_sent_at'        => 'datetime',
        'admin_email_failed_at'      => 'datetime',
        'buyer_confirmation_sent_at' => 'datetime',
        'buyer_confirmation_failed_at' => 'datetime',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function markRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }
}
