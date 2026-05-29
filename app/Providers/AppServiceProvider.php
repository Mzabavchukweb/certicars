<?php

namespace App\Providers;

use App\Models\Car;
use App\Models\ContactMessage;
use App\Observers\CarBrochureObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('admin.layouts.app', function ($view) {
            $view->with('unreadMessagesCount', ContactMessage::whereNull('read_at')->count());
        });

        Car::saved(function () {
            Cache::forget('sitemap.xml');
            Cache::forget('home.content');
            Cache::forget('catalog.filters');
        });
        Car::deleted(function () {
            Cache::forget('sitemap.xml');
            Cache::forget('home.content');
            Cache::forget('catalog.filters');
        });

        // Cached brochure stays in sync with each car's data. The observer
        // regenerates synchronously on admin save when something the
        // brochure cares about changes; admin waits ~5–10 s on save.
        // Errors are swallowed so a misbehaving Chromium can't bounce
        // legitimate admin saves.
        Car::observe(CarBrochureObserver::class);
    }
}
