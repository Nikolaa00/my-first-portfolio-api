<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function database(): JsonResponse
    {
        try {
            DB::connection()->getPdo();

            return response()->json([
                'status' => 'ok',
                'database' => [
                    'connected' => true,
                    'driver' => config('database.default'),
                    'name' => DB::connection()->getDatabaseName(),
                ],
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'database' => [
                    'connected' => false,
                    'driver' => config('database.default'),
                    'message' => $exception->getMessage(),
                ],
            ], 503);
        }
    }
}
