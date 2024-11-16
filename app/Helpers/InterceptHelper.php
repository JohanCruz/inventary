<?php
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

function intercept($controller, $method, ...$params)
{
    try {
        $reflection = new ReflectionMethod($controller, $method);
        $parameters = $reflection->getParameters();

        if (isset($parameters[0]) && $parameters[0]->getType()->getName() === Request::class) {
            array_unshift($params, app(Request::class));
        }

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
