<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . '/../routes/web.php', commands: __DIR__ . '/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [HandleInertiaRequests::class, AddLinkHeadersForPreloadedAssets::class]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response) {
            if (!request()->acceptsHtml()) {
                return $response;
            }

            if ($response->getStatusCode() === 403) {
                return Inertia::render('Errors/Forbidden')->toResponse(request())->setStatusCode(403);
            }

            if ($response->getStatusCode() === 404) {
                return Inertia::render('Errors/NotFound')->toResponse(request())->setStatusCode(404);
            }

            return $response;
        });
    })
    ->create();
