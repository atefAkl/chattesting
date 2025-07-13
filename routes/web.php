<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Auth::routes();

Route::middleware('auth')->group(function () {

    Route::prefix('users')->name('users-')->group(function () {
        Route::get('/profile/{id}', [UserController::class, 'showProfile'])->name('show-profile');
        Route::get('/send-friend-request/{id}', [UserController::class, 'sendFriendRequest'])->name('send-friend-request');
        Route::get('/send-message/{id}', [UserController::class, 'sendMessage'])->name('send-message');
    });

    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile');

    Route::get('/users/online', [ConversationController::class, 'onlineUsers'])->name('online-users');
    Route::get('/users/conversations', [ConversationController::class, 'index'])->name('conversations');
    Route::get('/users/conversations/{id}', [ConversationController::class, 'startChat'])->name('start-chat');
    Route::get('/users/conversations/{id}/send-message', [ConversationController::class, 'sendMessage'])->name('send-message');
    Route::get('/users/conversations/{id}/delete-message', [ConversationController::class, 'deleteMessage'])->name('delete-message');
    Route::get('/users/conversations/{id}/typing', [ConversationController::class, 'userTyping'])->name('user-typing');
    Route::get('/users/conversations/{id}/stop-typing', [ConversationController::class, 'userStopTyping'])->name('user-stop-typing');
});
