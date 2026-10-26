<?php

namespace App\Services\System;

use App\Contracts\RoleInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function __construct(protected RoleInterface $repository)
    {
        //
    }

    public function getAllRoles(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAllRoles($filters);
    }

    public function createRole(array $data): Role
    {
        return $this->repository->createRole($data);
    }

    public function getRole(int $id): Role
    {
        return $this->repository->getRole($id);
    }

    public function updateRole(int $id, array $data): Role
    {
        return $this->repository->updateRole($id, $data);
    }

    public function deleteRole(int $id): void
    {
        $this->repository->deleteRole($id);
    }
}
