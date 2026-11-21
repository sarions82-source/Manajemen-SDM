<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        then: function () {
            // Halaman utama tanpa sesi/cookie/CSRF: tampilan dimuat dari resources/page.html
            Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);
            // Alamat pendek untuk kandidat, misalnya /angkatan-2026 atau /disc-papi (dibaca oleh psikotes.js)
            Route::get('/{slug}', [\App\Http\Controllers\HomeController::class, 'index'])
                ->where('slug', '(?!api$)[a-z0-9-]{1,30}');
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
