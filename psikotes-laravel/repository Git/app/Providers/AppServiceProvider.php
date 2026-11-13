<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Pastikan file database SQLite ada (dibuat kosong jika belum ada).
        $path = config('database.connections.sqlite.database');
        if ($path && $path !== ':memory:' && !file_exists($path)) {
            @mkdir(dirname($path), 0775, true);
            @touch($path);
        }
    }
}
