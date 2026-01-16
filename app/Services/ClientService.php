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

    public function createClient(array $data): Client
    {
        return $this->repository->create($data);
    }
}
