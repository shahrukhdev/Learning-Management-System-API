<?php

namespace App\Services\Dashboard;

use App\Contracts\Dashboard\DashboardSummaryInterface;
use App\DTOs\Dashboard\DashboardSummaryDTO;

class DashboardSummaryService
{
    public function __construct(
        protected DashboardSummaryInterface $repository
    ) {}

    public function getDashboardSummary(DashboardSummaryDTO $dto): array
    {
        $user = auth()->user();

        $classIds = null;

        if ($user->hasRole('Teacher')) {
            $teacher = $user->teacher;

            $classIds = $teacher
                ? $teacher->academicClasses()->pluck('academic_classes.id')->all()
                : [];
        }

        return $this->repository->getDashboardSummary($dto, $classIds);
    }
}
