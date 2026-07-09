<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'path', 'route_name', 'session_id', 'visitor_id', 'device',
        'ip', 'referer', 'user_agent', 'utm_source', 'utm_medium', 'utm_campaign', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];
}
