<?php

namespace App\Services\System;

use App\Contracts\UserInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(protected UserInterface $repository)
    {
        //
    }

    public function getAllUsers(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAllUsers($filters);
    }

    public function createUser(array $data): User
    {
        return $this->repository->createUser($data);
    }

    public function getUser(int $id): User
    {
        return $this->repository->getUser($id);
    }

    public function updateUser(int $id, array $data): User
    {
        return $this->repository->updateUser($id, $data);
    }

    public function deleteUser(int $id): void
    {
        $this->repository->deleteUser($id);
    }
}