<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
                return true;
            }
            return false;
        });

        $app->make(Kernel::class)->bootstrap();

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
                return true;
            }
            return false;
        });

        return $app;
    }
}
