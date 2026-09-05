<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

    $middleware->redirectGuestsTo(fn () => null);

    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);



        // تسجيل ألقاب الـ Middlewares الخاصة بـ Spatie وبقية النظام
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

    }) 
    ->withExceptions(function (Exceptions $exceptions): void {

        /*
        |--------------------------------------------------------------------------
        | Authentication Exception (401) - غير مسجل الدخول
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated: Invalid or missing token.',
                ], Response::HTTP_UNAUTHORIZED);
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Authorization / Spatie Exception (403) - لا يملك الصلاحية/الدور
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (UnauthorizedException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden: You do not have the required role or permission for this dashboard.',
                ], Response::HTTP_FORBIDDEN);
            }
        });

    })
    ->create();
