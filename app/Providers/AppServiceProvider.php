<?php

namespace App\Providers;

use App\Models\Car;
use App\Models\ContactMessage;
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

        Car::saved(function (Car $car) {
            Cache::forget('sitemap.xml');
            Cache::forget('home.content');
            Cache::forget('catalog.filters');
            Cache::forget('car.show.' . $car->id);
        });
        Car::deleted(function (Car $car) {
            Cache::forget('sitemap.xml');
            Cache::forget('home.content');
            Cache::forget('catalog.filters');
            Cache::forget('car.show.' . $car->id);
        });
    }
}
