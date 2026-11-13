<?php

namespace App\Http\Controllers;

use App\Support\Psikotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $pdo = DB::connection()->getPdo();
            $service = new Psikotes($pdo, (string) config('psikotes.setup_key', ''), app()->environment('production'), (array) config('psikotes.staff_allow', []));
            $service->ensureSchema();
            $data = $service->handle(
                (string) $request->query('a', ''),
                $request->getMethod(),
                (string) $request->getContent(),
                $request->getScheme(),
                (int) $request->getPort(),
                (string) $request->ip()
            );
        } catch (\PDOException $e) {
            report($e);
            $data = ['ok' => false, 'err' => 'db'];
        }

        return response()->json($data, 200, [
            'Cache-Control' => 'no-store',
            'X-Content-Type-Options' => 'nosniff',
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
