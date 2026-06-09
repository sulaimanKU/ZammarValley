<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
        'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
         ]);
        $middleware->trustProxies(at: '*');


    })
    ->withExceptions(function (Exceptions $exceptions): void {
         $exceptions->renderable(function (UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied.'], 403);
            }

            return redirect()->route('index.dashboard')
                ->with('no_access', 'You do not have permission to access that page.');
        });

    })->create();
