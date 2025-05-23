<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\AuthenticateAdmin;
use App\Http\Middleware\CheckProductAvailability;
use App\Http\Middleware\NotAuthenticate;
use App\Http\Middleware\VaildatePayment;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            '/square-webhook',
        ]);
        $middleware->alias([
            'auth' => Authenticate::class,
            'not-auth' => NotAuthenticate::class,
            'check-product-availability' => CheckProductAvailability::class,
            'admin' => AuthenticateAdmin::class,
            'validate-payment' => VaildatePayment::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
