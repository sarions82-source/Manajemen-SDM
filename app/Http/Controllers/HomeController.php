<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return response(file_get_contents(resource_path('page.html')), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Frame-Options' => 'DENY',
            'Content-Security-Policy' => "frame-ancestors 'none'",
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'no-referrer',
        ]);
    }
}
