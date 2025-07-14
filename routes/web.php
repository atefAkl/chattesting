<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
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

    // الشات والمحادثات
    Route::prefix('conversations')->name('chat.')->group(function () {
        Route::get('/', [ConversationController::class, 'index'])->name('index');
        Route::get('/{user_id}', [ConversationController::class, 'startChat'])->name('start');
        Route::post('/{conversation_id}/send', [ConversationController::class, 'sendMessage'])->name('send');
        Route::get('/{conversation_id}/load-more', [ConversationController::class, 'loadMoreMessages'])->name('load-more');
        Route::post('/{conversation_id}/close', [ConversationController::class, 'closeChat'])->name('close');
    });
    
    // الإشعارات
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'getNotifications'])->name('get');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    });
});
