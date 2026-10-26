<?php

namespace App\Services\System;

use App\Contracts\PermissionInterface;
use Spatie\Permission\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PermissionService
{
    public function __construct(protected PermissionInterface $repository)
    {
        //
    }

    public function getAllPermissions(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAllPermissions($filters);
    }

    public function createPermission(array $data): Permission
    {
        return $this->repository->createPermission($data);
    }

    public function getPermission(int $id): Permission
    {
        return $this->repository->getPermission($id);
    }

    public function updatePermission(int $id, array $data): Permission
    {
        return $this->repository->updatePermission($id, $data);
    }

    public function deletePermission(int $id): void
    {
        $this->repository->deletePermission($id);
    }
}
