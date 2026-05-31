<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MemberController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['web'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    
    // admin routes
    Route::middleware(['can:admin-only'])->group(function () {

        // routes for GET, POST, PUT, DELETE /api/users
        Route::apiResource('users', UserController::class);
        
    });

        // staff routes
    Route::middleware(['can:access-front-desk'])->group(function () {

        //custom route for toggling member status
        Route::put('members/{member}/toggle-status', [MemberController::class, 'toggleStatus']);
        Route::put('/members/{member}/renew', [MemberController::class, 'renewMembership']);
        Route::put('/members/{member}/adjust-days', [MemberController::class, 'adjustDays']);

        // routes for GET, POST, PUT, DELETE /api/users
        Route::apiResource('members', MemberController::class);
        
    });
    
});
