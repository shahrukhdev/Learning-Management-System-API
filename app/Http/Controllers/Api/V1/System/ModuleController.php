<?php

namespace App\Http\Controllers\Api\V1\System;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Services\System\ModuleService;

class ModuleController extends Controller
{
    public function __construct(protected ModuleService $service)
    {
        //
    }

    public function index()
    {
        try {
            $modules = $this->service->getAllModules();

            return response()->json([
                'success' => true,
                'message' => 'Modules retrieved successfully.',
                'data' => $modules,
            ]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], $exception->getStatus());
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => [],
            ], 500);
        }
    }
}