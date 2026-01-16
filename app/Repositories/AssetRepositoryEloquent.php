<?php

namespace App\Repositories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Collection;

class AssetRepositoryEloquent implements AssetRepositoryInterface
{
    public function __construct(
        private readonly Asset $model
    ) {}

    public function getForSelect(): Collection
    {
        return $this->model->newQuery()
            ->select('id', 'symbol', 'name')
            ->orderBy('symbol')
            ->get();
    }
}
