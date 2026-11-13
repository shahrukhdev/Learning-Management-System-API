<?php

namespace App\Services\System;

use App\Contracts\UserInterface;
use App\Exceptions\ApiException;
use App\Models\User;
use App\Services\Academic\ParentService;
use App\Services\Academic\StudentService;
use App\Services\Academic\TeacherService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(
        protected UserInterface $repository,
        protected TeacherService $teacherService,
        protected ParentService $parentService,
        protected StudentService $studentService,
    ) {
        //
    }

    public function getAllUsers(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAllUsers($filters);
    }

    public function createUser(array $data): User
    {
        $classId = $this->pullClassId($data);
        $classIds = $this->pullClassIds($data);
        $year = $this->pullYear($data);

        return DB::transaction(function () use ($data, $classId, $classIds, $year) {
            $user = $this->repository->createUser($data);

            $this->syncRoleProfile($user, $classId, $classIds, $year);

            return $this->loadRelations($user);
        });
    }

    public function getUser(int $id): User
    {
        return $this->repository->getUser($id);
    }

    public function updateUser(int $id, array $data): User
    {
        $classId = $this->pullClassId($data);
        $classIds = $this->pullClassIds($data);
        $year = $this->pullYear($data);

        return DB::transaction(function () use ($id, $data, $classId, $classIds, $year) {
            $user = $this->repository->updateUser($id, $data);

            $this->syncRoleProfile($user, $classId, $classIds, $year);

            return $this->loadRelations($user);
        });
    }

    public function deleteUser(int $id): void
    {
        $user = $this->repository->getUser($id);

        DB::transaction(function () use ($user, $id) {
            $this->syncRoleProfileForDeletion($user);

            $this->repository->deleteUser($id);
        });
    }

    /**
     * class_id is not a users column; take it out of the payload.
     */
    private function pullClassId(array &$data): ?int
    {
        $classId = isset($data['class_id']) ? (int) $data['class_id'] : null;

        unset($data['class_id']);

        return $classId;
    }

    /**
     * year is a students column, not a users column; take it out of the payload.
     */
    private function pullYear(array &$data): ?int
    {
        $year = isset($data['year']) ? (int) $data['year'] : null;

        unset($data['year']);

        return $year;
    }

    private function pullClassIds(array &$data): array
    {
        $classIds = array_map('intval', $data['class_ids'] ?? []);

        unset($data['class_ids']);

        return $classIds;
    }

    private function loadRelations(User $user): User
    {
        return $user->load(['roles', 'student.academicClasses', 'teacher.academicClasses']);
    }

    private function syncRoleProfile(User $user, ?int $classId, array $classIds = [], ?int $year = null): void
    {
        $role = $user->roles->first()?->name;

        match ($role) {
            'Teacher' => $this->syncTeacherProfile($user, $classIds),

            'Parent' => $this->parentService->syncProfile($user->id),

            default => $this->deleteRoleProfiles($user->id),
        };

        if ($role === 'Student') {
            if ($classId === null) {
                throw new ApiException(
                    message: 'The class field is required for students.',
                    status: 422,
                    errors: ['class_id' => ['The class field is required for students.']],
                );
            }

            if ($year === null) {
                throw new ApiException(
                    message: 'The year field is required for students.',
                    status: 422,
                    errors: ['year' => ['The year field is required for students.']],
                );
            }

            $this->studentService->syncProfile($user, $classId, $year);
        } else {
            $this->studentService->deleteProfile($user->id);
        }
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

            'Student' => $this->studentService->deleteProfile($user->id),

            default => null,
        };
    }

    private function syncTeacherProfile(User $user, array $classIds): void
    {
        $this->teacherService->syncProfile($user->id);
        $this->teacherService->syncClasses($user->id, $classIds);
    }
}
