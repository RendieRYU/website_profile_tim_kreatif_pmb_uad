<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->server('HTTP_X_FORWARDED_PROTO') == 'https' || env('APP_ENV') != 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
        // Memaksa https jika menggunakan tunnel seperti localhost.run / ngrok
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && strpos($_SERVER['HTTP_X_FORWARDED_HOST'], '.lhr.life') !== false) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
