<?php

namespace App\Providers;

use App\Domains\Auth\Services\AuthService;
use App\Domains\User\Contracts\UserRepositoryInterface;
use App\Domains\User\Repositories\EloquentUserRepository;
use App\Domains\User\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register Repository Interfaces
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        // Register Domain Services
        $this->app->bind(AuthService::class);
        $this->app->bind(UserService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
