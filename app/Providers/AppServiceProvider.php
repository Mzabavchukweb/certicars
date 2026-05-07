<?php

namespace App\Providers;

use App\Models\ContactMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('admin.layouts.app', function ($view) {
            $view->with('unreadMessagesCount', ContactMessage::whereNull('read_at')->count());
        });
    }
}
