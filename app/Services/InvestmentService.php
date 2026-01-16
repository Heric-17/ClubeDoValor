<?php

namespace App\Services;

use App\Models\Investment;
use App\Repositories\InvestmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InvestmentService
{
    public function __construct(
        private readonly InvestmentRepositoryInterface $repository
    ) {}

    public function createInvestment(array $data): Investment
    {
        // Regra 1: Validar se o valor é positivo
        if (($data['amount'] ?? 0) < 0) {
            throw new \InvalidArgumentException('O valor do investimento não pode ser negativo.');
        }

        // Regra 2: Validar se a data não é futura
        $investmentDate = \Carbon\Carbon::parse($data['investment_date'] ?? now());
        if ($investmentDate->isFuture()) {
            throw new \InvalidArgumentException('A data do investimento não pode ser futura.');
        }

        return $this->repository->create($data);
    }

    public function getAllInvestments(): Collection
    {
        return $this->repository->getAll();
    }

    public function deleteInvestment(int $id): bool
    {
        // TODO: Implement business rules here
        // Exemplos de validações que podem ser adicionadas:
        // - Verificar se o investimento pode ser deletado
        // - Validar permissões do usuário
        
        return $this->repository->delete($id);
    }

    public function getInvestmentsByClient(int $clientId): Collection
    {
        return $this->repository->findByClient($clientId);
    }

    public function getInvestmentsByUser(int $userId, int $perPage = 15, ?int $clientId = null): LengthAwarePaginator
    {
        return $this->repository->getByUserId($userId, $perPage, $clientId);
    }

    public function getInvestmentStats(int $userId, ?int $clientId = null): array
    {
        $user = \App\Models\User::findOrFail($userId);

        $totalCurrentMonthQuery = $user->investments()
            ->whereYear('investment_date', now()->year)
            ->whereMonth('investment_date', now()->month);

        if ($clientId !== null) {
            $totalCurrentMonthQuery->where('client_id', $clientId);
        }

        $totalCurrentMonth = $totalCurrentMonthQuery->sum('amount');

        $topAssetQuery = \App\Models\Investment::query()
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
        ];
    }
}
