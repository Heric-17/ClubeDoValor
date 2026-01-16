<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface ClientRepositoryInterface
{
    public function getByUserId(int $userId): Collection;

    public function create(array $data): Client;
}
