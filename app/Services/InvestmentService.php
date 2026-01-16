<?php

namespace App\Services;

use App\Models\Investment;
use App\Repositories\InvestmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class InvestmentService
{
    public function __construct(
        private readonly InvestmentRepositoryInterface $repository
    ) {}

    public function createInvestment(array $data): Investment
    {
        // TODO: Implement business rules here
        // Exemplos de validações que podem ser adicionadas:
        // - Validar se o cliente existe
        // - Validar se o ativo existe
        // - Validar se o valor é positivo
        // - Validar regras de negócio específicas
        
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
}
