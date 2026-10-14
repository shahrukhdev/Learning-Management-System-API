<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserInterface
{
    public function getAllUsers(array $filters = []): LengthAwarePaginator;

    public function createUser(array $data): User;

    public function getUser(int $id): User;

    public function updateUser(int $id, array $data): User;

    public function deleteUser(int $id): void;
}