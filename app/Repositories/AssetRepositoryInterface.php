<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface AssetRepositoryInterface
{
    public function getForSelect(): Collection;
}
