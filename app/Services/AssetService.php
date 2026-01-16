<?php

namespace App\Services;

use App\Repositories\AssetRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AssetService
{
    public function __construct(
        private readonly AssetRepositoryInterface $repository
    ) {}

    public function getAssetsForSelect(): Collection
    {
        return $this->repository->getForSelect();
    }
}
