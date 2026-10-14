<?php

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

interface RoleInterface
{
    public function getAllRoles(array $filters = []): LengthAwarePaginator;

    public function createRole(array $data): Role;

    public function getRole(int $id): Role;

    public function updateRole(int $id, array $data): Role;

    public function deleteRole(int $id): void;
}