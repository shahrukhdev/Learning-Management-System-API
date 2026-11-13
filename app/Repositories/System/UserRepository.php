<?php

namespace App\Repositories\System;

use App\Contracts\UserInterface;
use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserInterface
{
    public function getAllUsers(array $filters = []): LengthAwarePaginator
    {
        // Same relations as the single user, so every row has year / class (students)
        // and class_ids (teachers).
        $query = User::query()
            ->with([
                'roles',
                'student.academicClasses',
                'teacher.academicClasses',
            ]);

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['role_id'])) {
            $query->whereHas('roles', function ($query) use ($filters) {
                $query->where('roles.id', $filters['role_id']);
            });
        }

        // sort_direction (asc|desc) sorts by name; without it, latest first
        // (id breaks ties between users created in the same second).
        $sortDirection = $filters['sort_direction'] ?? null;

        if (in_array($sortDirection, ['asc', 'desc'], true)) {
            $query->orderBy('name', $sortDirection);
        } else {
            $query->latest()->orderByDesc('id');
        }

        $perPage = min(max((int) ($filters['per_page'] ?? 10), 1), 100);

        return $query->paginate($perPage);
    }

    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $roles = $data['roles'] ?? [];

            unset($data['roles']);

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            if (!empty($roles)) {
                $user->syncRoles($roles);
            }

            return $user->load('roles');
        });
    }

    public function getUser(int $id): User
    {
        $user = User::with([
            'roles',
            'student.academicClasses',
            'teacher.academicClasses',
        ])->find($id);

        if (!$user) {
            throw new ApiException(
                message: 'User not found.',
                status: 404
            );
        }

        return $user;
    }

    public function updateUser(int $id, array $data): User
    {
        return DB::transaction(function () use ($id, $data) {
            $user = User::find($id);

            if (!$user) {
                throw new ApiException(
                    message: 'User not found.',
                    status: 404
                );
            }

            if (array_key_exists('roles', $data)) {
                $user->syncRoles($data['roles']);
                unset($data['roles']);
            }

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            return $user->load('roles');
        });
    }

    public function deleteUser(int $id): void
    {
        $user = User::find($id);

        if (!$user) {
            throw new ApiException(
                message: 'User not found.',
                status: 404
            );
        }

        $user->delete();
    }
}