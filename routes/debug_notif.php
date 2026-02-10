<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/debug-notif', function () {
    $notif = DB::table('notification')->orderBy('created_at', 'desc')->first();
    return response()->json($notif);
});
