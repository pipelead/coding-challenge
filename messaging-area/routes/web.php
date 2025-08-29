<?php

use App\Modules\Messaging\Controllers\MessagingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MessagingController::class, 'index'])->name('messaging.index');
Route::post('/conversations/{conversation}/messages', [MessagingController::class, 'sendMessage'])->name('messaging.send');
