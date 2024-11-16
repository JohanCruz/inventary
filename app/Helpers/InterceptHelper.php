<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('intercept')) {
    function intercept($controller, $method, ...$params)
    {
        try {
            return call_user_func_array([$controller, $method], $params);
        } catch (\Throwable $e) {
            Log::error('Error in ' . $method, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'An error occurred. Please try again later.',
            ], 500);
        }
    }
}
