<?php

namespace App\Repositories;

use App\Models\Investment;
use Illuminate\Database\Eloquent\Collection;

interface InvestmentRepositoryInterface
{
    public function getAll(): Collection;

    public function create(array $data): Investment;

    public function delete(int $id): bool;

    public function findByClient(int $clientId): Collection;
}
