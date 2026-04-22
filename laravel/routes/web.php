<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;

Route::get('/{any}', function () {
    return view('welcome'); // or 'app' if you created app.blade.php
})->where('any', '.*');

Route::get('/admin', function () {
    // ...
})->middleware(AdminMiddleware::class);