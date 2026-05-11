<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::get('/test', function() {
    return response()->json(['status' => 'ok', 'message' => 'API Ready']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);