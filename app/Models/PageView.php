<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = ['path', 'route_name', 'session_id', 'ip', 'referer', 'user_agent', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];
}
