<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\AuthInterface;
use App\Contracts\PermissionInterface;
use App\Contracts\ProfileInterface;
use App\Contracts\RoleInterface;
use App\Contracts\UserInterface;
use App\Repositories\AuthRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthInterface::class, AuthRepository::class);
        $this->app->bind(PermissionInterface::class, PermissionRepository::class);
        $this->app->bind(ProfileInterface::class, ProfileRepository::class);
        $this->app->bind(RoleInterface::class, RoleRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
