<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Member;
use App\Mail\MemberQrCodeMail;

if (app()->isLocal()) {
    Route::get('/dev/qr-email-preview', function () {
        $member = Member::first() ?? new Member([
            'first_name' => 'Dez',
            'email' => 'oragelemon25@gmail.com',
            'qr_token' => 'GYM-TEST-12345'
        ]);
        return new MemberQrCodeMail($member);
    });
}

Route::get('/{any}', function () {
    return view('welcome'); // or 'app' if you created app.blade.php
})->where('any', '.*');

Route::get('/admin', function () {
    // ...
})->middleware(AdminMiddleware::class);