<?php

use App\Providers\Afspraak\Read;
use Illuminate\Database\ConnectionException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $isDatabaseConnectionFailure = function (Throwable $exception): bool {
            if ($exception instanceof ConnectionException) {
                return true;
            }

            if (! $exception instanceof QueryException) {
                return false;
            }

            $driverCode = (int) ($exception->errorInfo[1] ?? 0);

            if (in_array($driverCode, [1044, 1045, 1049, 2002, 2003, 2005, 2006], true)) {
                return true;
            }

            $message = strtolower($exception->getMessage());

            return str_contains($message, 'could not find driver')
                || str_contains($message, 'unable to open database file');
        };

        $renderAfspraakFout = fn (): Response => response()->view('afspraken.overzicht', [
            'afspraken' => collect(),
            'foutmelding' => Read::FOUTMELDING,
        ]);

        $exceptions->render(function (QueryException $exception, Request $request) use ($isDatabaseConnectionFailure, $renderAfspraakFout): Response|false {
            if (! $request->routeIs('afspraken.overzicht') || ! $isDatabaseConnectionFailure($exception)) {
                return false;
            }

            return $renderAfspraakFout();
        });

        $exceptions->render(function (ConnectionException $exception, Request $request) use ($renderAfspraakFout): Response|false {
            if (! $request->routeIs('afspraken.overzicht')) {
                return false;
            }

            return $renderAfspraakFout();
        });
    })->create();
