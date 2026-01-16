<?php

namespace App\Services;

use App\Models\Client;
use App\Repositories\ClientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ClientService
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository
    ) {}

    public function getClientsByUser(int $userId): Collection
    {
        return $this->repository->getByUserId($userId);
    }

    public function getClientsForSelect(int $userId): Collection
    {
        return $this->repository->getForSelect($userId);
    }

    public function getClientById(int $id): Client
    {
        return $this->repository->findById($id);
    }

    public function createClient(array $data): Client
    {
        $userId = $data['user_id'] ?? null;

        if ($userId === null) {
            throw new \InvalidArgumentException('user_id é obrigatório para criar um cliente.');
        }

        if (isset($data['email']) && $this->repository->emailExistsForUser($data['email'], $userId)) {
            throw new \InvalidArgumentException('Já existe um cliente com este email para este usuário.');
        }

        return $this->repository->create($data);
    }

    public function updateClient(int $id, array $data): Client
    {
        $client = $this->repository->findById($id);
        $userId = $client->user_id;

        if (isset($data['email']) && $this->repository->emailExistsForUserExcludingId($data['email'], $userId, $id)) {
            throw new \InvalidArgumentException('Já existe um cliente com este email para este usuário.');
        }

        return $this->repository->update($id, $data);
    }

    public function deleteClient(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
