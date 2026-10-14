<?php

namespace App\Contracts;

use Spatie\Permission\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PermissionInterface
{
    public function getAllPermissions(array $filters = []): LengthAwarePaginator;

    public function createPermission(array $data): Permission;

    public function getPermission(int $id): Permission;

    public function updatePermission(int $id, array $data): Permission;

    public function deletePermission(int $id): void;
}
