<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        /*
        |--------------------------------------------------------------------------
        | Authentication Exception (401)
        |--------------------------------------------------------------------------
        |
        | يرجع JSON بدل صفحة HTML عند عدم تسجيل الدخول
        |
        */

        $exceptions->render(function (
            AuthenticationException $e,
            $request
        ) {

            if ($request->expectsJson()) {

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], Response::HTTP_UNAUTHORIZED);

            }

        });


        /*
        |--------------------------------------------------------------------------
        | Authorization Exception (403)
        |--------------------------------------------------------------------------
        |
        | يرجع JSON بدل صفحة HTML عند عدم وجود صلاحية
        |
        */

        $exceptions->render(function (
            AuthorizationException $e,
            $request
        ) {

            if ($request->expectsJson()) {

                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden.',
                ], Response::HTTP_FORBIDDEN);

            }

        });

    })
    ->create();