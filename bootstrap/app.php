<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\TrackPageView::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // CSRF / session-expired (419) — return a friendly Polish message
        // instead of the generic Page Expired screen. Critical for the
        // long-lived admin car wizard where a user may leave the form open
        // past session lifetime and lose work.
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            $msg = 'Sesja wygasła. Odśwież stronę i spróbuj ponownie.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg, 'session_expired' => true], 419);
            }
            // Add ?session_expired=1 so the wizard restore banner knows to surface a
            // dedicated message AND so the autosave layer keeps the draft instead of
            // assuming a successful save cleared it.
            $back = redirect()->back()
                ->withInput($request->except(['_token', 'password', 'password_confirmation']))
                ->with('error', $msg)
                ->with('session_expired', true);
            return $back;
        });
    })->create();
