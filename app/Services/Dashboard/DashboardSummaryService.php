
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
        return $this->repository->getDashboardSummary($dto);
    }
}
