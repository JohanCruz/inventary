<?php
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

function intercept($controller, $method, ...$params)
{
    try {
        $reflection = new ReflectionMethod($controller, $method);
        $parameters = $reflection->getParameters();

        // Si el primer parámetro requiere Request, lo inyectamos automáticamente
        if (isset($parameters[0]) && $parameters[0]->getType() && $parameters[0]->getType()->getName() === Illuminate\Http\Request::class) {
            array_unshift($params, app(Illuminate\Http\Request::class));
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
