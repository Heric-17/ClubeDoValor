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

    public function create(array $data): Client
    {
        return $this->model->create($data);
    }
}
