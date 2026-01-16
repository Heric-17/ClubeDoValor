<?php

namespace App\Repositories;

use App\Models\Investment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    public function getByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        $user = User::findOrFail($userId);

        return $user->investments()
            ->with(['client', 'asset'])
            ->orderBy('investment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
