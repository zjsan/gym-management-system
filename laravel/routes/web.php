<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('welcome'); // or 'app' if you created app.blade.php
})->where('any', '.*');
