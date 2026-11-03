<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\TeacherMiddleware;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\TeacherPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__.'/../routes/api/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'admin.permission' => \App\Http\Middleware\AdminPermissionMiddleware::class,
            'teacher' => TeacherMiddleware::class,
            'student' => StudentMiddleware::class,
            'teacher.permission' => TeacherPermissionMiddleware::class,
            'student.active' => \App\Http\Middleware\Api\Student\EnsureAccountIsActive::class,
            'student.verified' => \App\Http\Middleware\Api\Student\EnsureAccountIsVerified::class,
            'parent.only'      => \App\Http\Middleware\Api\Student\EnsureIsParent::class,
            'student.only'     => \App\Http\Middleware\Api\Student\EnsureIsStudent::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
