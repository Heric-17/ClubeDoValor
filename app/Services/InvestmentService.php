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

    public function getInvestmentById(int $id): Investment
    {
        return $this->repository->findById($id);
    }

    public function updateInvestment(int $id, array $data): Investment
    {
        // Regra 1: Validar se o valor é positivo
        if (isset($data['amount']) && $data['amount'] < 0) {
            throw new \InvalidArgumentException('O valor do investimento não pode ser negativo.');
        }

        // Regra 2: Validar se a data não é futura
        if (isset($data['investment_date'])) {
            $investmentDate = \Carbon\Carbon::parse($data['investment_date']);
            if ($investmentDate->isFuture()) {
                throw new \InvalidArgumentException('A data do investimento não pode ser futura.');
            }
        }

        return $this->repository->update($id, $data);
    }

    public function deleteInvestment(int $id): bool
    {
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
        return $this->repository->getStats($userId, $clientId);
    }
}
