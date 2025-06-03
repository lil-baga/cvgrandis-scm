<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware; // Pastikan ini di-import

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan alias untuk route middleware Anda di sini
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            // Tambahkan alias middleware lain jika ada, contoh:
            // 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            // 'auth' => \App\Http\Middleware\Authenticate::class, // Biasanya sudah ada atau ditangani berbeda
        ]);

        // Anda juga bisa menambahkan middleware ke grup tertentu di sini jika perlu,
        // meskipun untuk alias, cukup di atas.
        // $middleware->web(append: [
        //     // \App\Http\Middleware\YourCustomWebMiddleware::class,
        // ]);

        // $middleware->api(prepend: [
        //     // \App\Http\Middleware\YourCustomApiMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();