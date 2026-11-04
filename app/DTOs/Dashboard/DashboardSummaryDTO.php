<?php

namespace App\DTOs\Dashboard;

use Illuminate\Http\Request;

readonly class DashboardSummaryDTO
{
    public function __construct(
        public string $attendancePeriod,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            attendancePeriod: $request->validated('attendance_period') ?? '7days',
        );
    }
}
