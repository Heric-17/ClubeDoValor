<?php

namespace App\Repositories;

use App\Models\Investment;
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

    public function findById(int $id): Investment
    {
        return $this->model->findOrFail($id);
    }

    public function update(int $id, array $data): Investment
    {
        $investment = $this->model->findOrFail($id);
        $investment->update($data);

        return $investment->fresh();
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

    public function getByUserId(int $userId, int $perPage = 15, ?int $clientId = null): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->join('clients', 'investments.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->whereNull('clients.deleted_at')
            ->with(['client', 'asset'])
            ->select('investments.*');

        if ($clientId !== null) {
            $query->where('investments.client_id', $clientId);
        }

        return $query
            ->orderBy('investments.investment_date', 'desc')
            ->orderBy('investments.created_at', 'desc')
            ->paginate($perPage);
    }

    public function getStats(int $userId, ?int $clientId = null): array
    {
        $totalCurrentMonthQuery = $this->model->newQuery()
            ->join('clients', 'investments.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->whereNull('clients.deleted_at')
            ->whereYear('investments.investment_date', now()->year)
            ->whereMonth('investments.investment_date', now()->month);

        if ($clientId !== null) {
            $totalCurrentMonthQuery->where('investments.client_id', $clientId);
        }

        $totalCurrentMonth = $totalCurrentMonthQuery->sum('investments.amount');

        $topAssetQuery = $this->model->newQuery()
            ->join('clients', 'investments.client_id', '=', 'clients.id')
            ->join('assets', 'investments.asset_id', '=', 'assets.id')
            ->where('clients.user_id', $userId)
            ->whereNull('clients.deleted_at');

        if ($clientId !== null) {
            $topAssetQuery->where('investments.client_id', $clientId);
        }

        $topAsset = $topAssetQuery
            ->selectRaw('assets.symbol, SUM(investments.amount) as total_amount')
            ->groupBy('assets.id', 'assets.symbol')
            ->orderByDesc('total_amount')
            ->first();

        return [
            'total_current_month' => (float) $totalCurrentMonth,
            'top_asset' => $topAsset?->symbol ?? null,
            'top_asset_amount' => $topAsset ? (float) $topAsset->total_amount : null,
        ];
    }
}
