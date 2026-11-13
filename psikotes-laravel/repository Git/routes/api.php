<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

// Semua permintaan aplikasi masuk ke /api?a=... (GET atau POST)
Route::match(['GET', 'POST'], '/', [ApiController::class, 'handle']);
