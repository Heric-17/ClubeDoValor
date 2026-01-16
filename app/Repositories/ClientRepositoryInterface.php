<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface ClientRepositoryInterface
{
    public function getByUserId(int $userId): Collection;

    public function getForSelect(int $userId): Collection;

    public function create(array $data): Client;

    public function update(int $id, array $data): Client;

    public function delete(int $id): bool;

    public function findById(int $id): Client;

    public function emailExistsForUser(string $email, int $userId): bool;

    public function emailExistsForUserExcludingId(string $email, int $userId, int $excludeId): bool;
}
