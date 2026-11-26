<?php

namespace App\Providers;

use App\Support\SiteSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(SiteSettings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(fn () => Password::min(12)->max(255));

        View::composer(['site.*', 'components.layouts.site'], function ($view): void {
            $view->with('settings', $this->app->make(SiteSettings::class));
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip())->response(
                fn () => redirect()->to(route('home').'#contact-form')
                    ->withInput()
                    ->withErrors(['form' => 'Je hebt kort na elkaar veel berichten verstuurd. Probeer het over een paar minuten opnieuw, of bel ons.'])
            );
        });
    }
}
