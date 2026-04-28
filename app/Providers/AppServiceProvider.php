<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use MongoDB\Laravel\Sanctum\PersonalAccessToken; // Pastikan import ini ada

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Mengubah model default Sanctum ke MongoDB
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
