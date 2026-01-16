<?php

namespace App\Repositories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Collection;

interface AssetRepositoryInterface
{
    public function getForSelect(): Collection;
}
