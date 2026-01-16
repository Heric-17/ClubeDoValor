<?php

namespace App\Providers;

use App\Repositories\ClientRepositoryEloquent;
use App\Repositories\ClientRepositoryInterface;
use App\Repositories\InvestmentRepositoryEloquent;
use App\Repositories\InvestmentRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(
            InvestmentRepositoryInterface::class,
            InvestmentRepositoryEloquent::class
        );

        $this->app->bind(
            ClientRepositoryInterface::class,
            ClientRepositoryEloquent::class
        );
    }

    public function boot(): void
    {
        //
    }
}
