
<?php

namespace App\Http\Controllers\Api\V1\Academic;

use App\DTOs\Academic\MarkAttendanceDTO;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Academic\GetAttendanceOverviewRequest;
use App\Http\Requests\Api\V1\Academic\GetClassAttendanceRequest;
use App\Http\Requests\Api\V1\Academic\MarkAttendanceRequest;
use App\Http\Resources\Api\V1\Academic\AttendanceOverviewResource;
use App\Http\Resources\Api\V1\Academic\AttendanceResource;
use App\Services\Academic\AttendanceService;
use Exception;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $service
    ) {}

    public function index(GetClassAttendanceRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $students = $this->service->getClassAttendance(
                $validated['academic_class_id'],
                $validated['attendance_date']
            );

            return response()->json([
                'success' => true,
                'message' => 'Class attendance retrieved successfully.',
                'data' => AttendanceResource::collection($students),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function store(MarkAttendanceRequest $request): JsonResponse
    {
        try {
            $students = $this->service->markAttendance(
                MarkAttendanceDTO::fromRequest($request)
            );

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully.',
                'data' => AttendanceResource::collection($students),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }

    public function overview(GetAttendanceOverviewRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $students = $this->service->getAttendanceOverview(
                $validated['academic_class_id'],
                $validated['week_start']
            );

            return response()->json([
                'success' => true,
                'message' => 'Attendance overview retrieved successfully.',
                'data' => AttendanceOverviewResource::collection($students),
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }
}
