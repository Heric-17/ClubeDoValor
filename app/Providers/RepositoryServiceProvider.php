<?php

namespace App\Providers;

use App\Repositories\AssetRepositoryEloquent;
use App\Repositories\AssetRepositoryInterface;
use App\Repositories\ClientRepositoryEloquent;
use App\Repositories\ClientRepositoryInterface;
use App\Repositories\InvestmentRepositoryEloquent;
use App\Repositories\InvestmentRepositoryInterface;
use App\Repositories\UserRepositoryEloquent;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepositoryEloquent::class
        );

        $this->app->bind(
            InvestmentRepositoryInterface::class,
            InvestmentRepositoryEloquent::class
        );

        $this->app->bind(
            ClientRepositoryInterface::class,
            ClientRepositoryEloquent::class
        );

        $this->app->bind(
            AssetRepositoryInterface::class,
            AssetRepositoryEloquent::class
        );
    }

    public function boot(): void
    {
        //
    }
}
