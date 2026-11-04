<?php

namespace App\Services\System;

use App\Contracts\UserInterface;
use App\Models\User;
use App\Services\Academic\TeacherService;
use App\Services\Academic\ParentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        protected UserInterface $repository,
        protected TeacherService $teacherService,
        protected ParentService $parentService,
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

            'Parent' => $this->parentService->syncProfile($user->id),

            default => $this->deleteRoleProfiles($user->id),
        };
    }

    private function deleteRoleProfiles(int $userId): void
    {
        $this->teacherService->deleteProfile($userId);
        $this->parentService->deleteProfile($userId);
    }

    private function syncRoleProfileForDeletion(User $user): void
    {
        match ($user->roles->first()?->name) {
            'Teacher' => $this->teacherService->deleteProfile($user->id),

            'Parent' => $this->parentService->deleteProfile($user->id),

            default => null,
        };
    }
}
