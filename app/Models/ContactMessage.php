<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'message', 'ip', 'user_agent', 'read_at',
        'is_spam', 'spam_score', 'spam_reasons', 'body_hash'];

    protected $casts = [
        'read_at'      => 'datetime',
        'is_spam'      => 'boolean',
        'spam_reasons' => 'array',
    ];

    /** Skrzynka = wiadomości, które przeszły filtr antyspamowy. */
    public function scopeInbox($query)
    {
        return $query->where('is_spam', false);
    }

    public function scopeSpam($query)
    {
        return $query->where('is_spam', true);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at')->where('is_spam', false);
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
