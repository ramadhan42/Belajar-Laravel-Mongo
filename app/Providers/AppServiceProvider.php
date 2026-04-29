<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Ini memaksa Sanctum untuk tahu bahwa dia harus memakai model kita (MongoDB)
        // bukan model bawaan SQL.
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
