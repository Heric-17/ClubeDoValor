<?php

namespace App\Providers;

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
    }

    public function boot(): void
    {
        //
    }
}
