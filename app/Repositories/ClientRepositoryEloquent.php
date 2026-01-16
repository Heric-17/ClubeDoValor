<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ClientRepositoryEloquent implements ClientRepositoryInterface
{
    public function __construct(
        private readonly Client $model
    ) {}

    public function getByUserId(int $userId): Collection
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get();
    }

    public function getForSelect(int $userId): Collection
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): Client
    {
        return $this->model->create($data);
    }

    public function findById(int $id): Client
    {
        return $this->model->findOrFail($id);
    }

    public function update(int $id, array $data): Client
    {
        $client = $this->model->findOrFail($id);
        $client->update($data);

        return $client->fresh();
    }

    public function delete(int $id): bool
    {
        $client = $this->model->findOrFail($id);

        return $client->delete();
    }

    public function emailExistsForUser(string $email, int $userId): bool
    {
        return $this->model->newQuery()
            ->where('email', $email)
            ->where('user_id', $userId)
            ->exists();
    }

    public function emailExistsForUserExcludingId(string $email, int $userId, int $excludeId): bool
    {
        return $this->model->newQuery()
            ->where('email', $email)
            ->where('user_id', $userId)
            ->where('id', '!=', $excludeId)
            ->exists();
    }
}
