<?php

namespace App\Contracts\Dashboard;

use App\DTOs\Dashboard\DashboardSummaryDTO;

interface DashboardSummaryInterface
{
    public function getDashboardSummary(DashboardSummaryDTO $dto): array;
}
