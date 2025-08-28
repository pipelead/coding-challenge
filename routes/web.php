<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ConversationController::class, 'index'])->name('conversations.index');
Route::get('/conversations/{contact}', [ConversationController::class, 'show'])->name('conversations.show');
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
Route::get('/conversations/{contact}/messages/updates', [MessageController::class, 'updates']);
