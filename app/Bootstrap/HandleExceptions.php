<?php

namespace App\Bootstrap;

use Illuminate\Foundation\Bootstrap\HandleExceptions as BaseHandleExceptions;

class HandleExceptions extends BaseHandleExceptions
{
    /**
     * Report and handle PHP errors, safely suppressing deprecations on modern PHP runtimes.
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if ($level === E_DEPRECATED || $level === E_USER_DEPRECATED) {
            return true;
        }

        return parent::handleError($level, $message, $file, $line, $context);
    }
}
