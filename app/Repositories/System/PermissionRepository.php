<?php

namespace App\Repositories\System;

use App\Contracts\PermissionInterface;
use App\Exceptions\ApiException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;

class PermissionRepository implements PermissionInterface
{
    public function getAllPermissions(array $filters = []): LengthAwarePaginator
    {
        $query = Permission::query()
            ->where('guard_name', 'web');

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = min(max((int) ($filters['per_page'] ?? 15), 1), 100);

        return $query->latest()->paginate($perPage);
    }

    public function createPermission(array $data): Permission
    {
        $data['guard_name'] = 'web';

        return Permission::create($data);
    }

    public function getPermission(int $id): Permission
    {
        $permission = Permission::where('guard_name', 'web')
            ->find($id);

        if (!$permission) {
            throw new ApiException(
                message: 'Permission not found.',
                status: 404
            );
        }

        return $permission;
    }

    public function updatePermission(int $id, array $data): Permission
    {
        $permission = Permission::where('guard_name', 'web')
            ->find($id);

        if (!$permission) {
            throw new ApiException(
                message: 'Permission not found.',
                status: 404
            );
        }

        $permission->update($data);

        return $permission;
    }

    public function deletePermission(int $id): void
    {
        $permission = Permission::where('guard_name', 'web')
            ->find($id);

        if (!$permission) {
            throw new ApiException(
                message: 'Permission not found.',
                status: 404
            );
        }

        $permission->delete();
    }
}