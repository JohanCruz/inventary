<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class LogHelper
{
    /**
     * Intercept a controller method and log any exceptions that occur.
     *
     * @param  \Closure  $callback
     * @param  string  $logName
     * @return mixed
     */
    public static function intercept(\Closure $callback, $logName = 'application')
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            static::logException($e, $logName);
            throw $e;
        }
    }

    /**
     * Log an exception to the specified log channel.
     *
     * @param  \Exception  $exception
     * @param  string  $logName
     * @return void
     */
    protected static function logException(\Exception $exception, $logName)
    {
        Log::channel($logName)->error($exception);
    }
}