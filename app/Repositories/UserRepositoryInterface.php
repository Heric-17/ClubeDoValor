<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findById(int $id): User;
}
