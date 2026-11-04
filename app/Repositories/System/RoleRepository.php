<?php

namespace App\Repositories\System;

use App\Contracts\RoleInterface;
use App\Exceptions\ApiException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleInterface
{
    public function getAllRoles(array $filters = []): LengthAwarePaginator
    {
        dd([
    'guard' => config('auth.guards.web'),
    'provider' => config('auth.providers.users'),
    'model' => config('auth.providers.users.model'),
]);
        $query = Role::query()
            ->where('guard_name', 'web')
            ->with('permissions')
            ->withCount('users');
        dd($query->get());

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = min(max((int) ($filters['per_page'] ?? 10), 1), 100);

        return $query->latest()->paginate($perPage);
    }

    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $permissions = $data['permissions'] ?? [];

            unset($data['permissions']);

            $data['guard_name'] = 'web';

            $role = Role::create($data);

            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
            }

            return $role->load('permissions');
        });
    }

    public function getRole(int $id): Role
    {
        $role = Role::where('guard_name', 'web')
            ->with('permissions')
            ->find($id);

        if (!$role) {
            throw new ApiException(
                message: 'Role not found.',
                status: 404
            );
        }

        return $role;
    }

    public function updateRole(int $id, array $data): Role
    {
        return DB::transaction(function () use ($id, $data) {
            $role = Role::where('guard_name', 'web')->find($id);

            if (!$role) {
                throw new ApiException(
                    message: 'Role not found.',
                    status: 404
                );
            }

            if (array_key_exists('permissions', $data)) {
                $role->syncPermissions($data['permissions']);
                unset($data['permissions']);
            }

            $role->update($data);

            return $role->load('permissions');
        });
    }

    public function deleteRole(int $id): void
    {
        $role = Role::where('guard_name', 'web')->find($id);

        if (!$role) {
            throw new ApiException(
                message: 'Role not found.',
                status: 404
            );
        }

        $role->delete();
    }
}