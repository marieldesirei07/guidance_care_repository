<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // One password policy for every place a password is set:
        // at least 8 characters, upper + lower case, a number and a symbol.
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers()->symbols());
    }
}
