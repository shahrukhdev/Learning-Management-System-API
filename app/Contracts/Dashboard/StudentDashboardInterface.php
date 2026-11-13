<?php

namespace App\Contracts\Dashboard;

use App\Models\Attendance;
use App\Models\HifzSession;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface StudentDashboardInterface
{
    /**
     * The active student record of the given user (with user and classes), if any.
     */
    public function findStudentByUserId(int $userId): ?Student;

    public function getTodayAttendance(int $studentId): ?Attendance;

    /**
     * Latest hifz session that has new sabaq entries.
     */
    public function getLatestNewSabaqSession(int $studentId): ?HifzSession;

    /**
     * Latest hifz session that has manzil entries.
     */
    public function getLatestManzilSession(int $studentId): ?HifzSession;

    /**
     * One attendance status per date (Y-m-d => status) between the given dates.
     *
     * @return Collection<string, string>
     */
    public function getAttendanceStatusesByDate(int $studentId, Carbon $startDate, Carbon $endDate): Collection;
}
