<?php

namespace App\Repositories;

use App\Models\User;

class UserRepositoryEloquent implements UserRepositoryInterface
{
    public function __construct(
        private readonly User $model
    ) {}

    public function findById(int $id): User
    {
        return $this->model->findOrFail($id);
    }
}
