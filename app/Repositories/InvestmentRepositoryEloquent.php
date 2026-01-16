<?php

namespace App\Repositories;

use App\Models\Investment;
use Illuminate\Database\Eloquent\Collection;

class InvestmentRepositoryEloquent implements InvestmentRepositoryInterface
{
    public function __construct(
        private readonly Investment $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->newQuery()->get();
    }

    public function create(array $data): Investment
    {
        return $this->model->create($data);
    }

    public function delete(int $id): bool
    {
        $investment = $this->model->findOrFail($id);

        return $investment->delete();
    }

    public function findByClient(int $clientId): Collection
    {
        return $this->model->newQuery()
            ->where('client_id', $clientId)
            ->get();
    }
}
