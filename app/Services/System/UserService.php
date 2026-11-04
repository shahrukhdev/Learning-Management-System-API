<?php

namespace App\Services\System;

use App\Contracts\UserInterface;
use App\Models\User;
use App\Services\Academic\TeacherService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        protected UserInterface $repository,
        protected TeacherService $teacherService,
    ) {
        //
    }

    public function getAllUsers(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAllUsers($filters);
    }

    public function createUser(array $data): User
    {
        $user = $this->repository->createUser($data);

        $this->syncRoleProfile($user);

        return $user->load('roles');
    }

    public function getUser(int $id): User
    {
        return $this->repository->getUser($id);
    }

    public function updateUser(int $id, array $data): User
    {
        $user = $this->repository->updateUser($id, $data);

        $this->syncRoleProfile($user);

        return $user->load('roles');
    }

    public function deleteUser(int $id): void
    {
        $user = $this->repository->getUser($id);

        $this->syncRoleProfileForDeletion($user);

        $this->repository->deleteUser($id);
    }

    private function syncRoleProfile(User $user): void
    {
        $role = $user->roles->first()?->name;

        match ($role) {
            'Teacher' => $this->teacherService->syncProfile($user->id),

            default => $this->teacherService->deleteProfile($user->id),
        };
    }

    private function syncRoleProfileForDeletion(User $user): void
    {
        match ($user->roles->first()?->name) {
            'Teacher' => $this->teacherService->deleteProfile($user->id),
            default => null,
        };
    }
}
