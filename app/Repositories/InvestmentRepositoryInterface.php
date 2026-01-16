<?php

namespace App\Repositories;

use App\Models\Investment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InvestmentRepositoryInterface
{
    public function getAll(): Collection;

    public function create(array $data): Investment;

    public function delete(int $id): bool;

    public function findByClient(int $clientId): Collection;

    public function getByUserId(int $userId, int $perPage = 15, ?int $clientId = null): LengthAwarePaginator;
}
