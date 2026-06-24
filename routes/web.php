<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cabina/notifications/count', function () {
    return response()->json([
        'count' => auth()->user()
            ? auth()->user()->unreadNotifications()->count()
            : 0,
    ]);
})->middleware(['web', 'auth'])->name('cabina.notifications.count');