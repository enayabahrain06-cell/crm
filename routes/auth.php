<?php

use App\Http\Controllers\Web\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Web\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Web\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Web\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Web\Auth\NewPasswordController;
use App\Http\Controllers\Web\Auth\PasswordController;
use App\Http\Controllers\Web\Auth\PasswordResetLinkController;
use App\Http\Controllers\Web\Auth\RegisteredUserController;
use App\Http\Controllers\Web\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('auth/register', [RegisteredUserController::class, 'create'])->name('auth.register');
    Route::post('auth/register', [RegisteredUserController::class, 'store']);

    Route::get('auth/login', [AuthenticatedSessionController::class, 'create'])->name('auth.login');
    Route::post('auth/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('auth/forgot-password', [PasswordResetLinkController::class, 'create'])->name('auth.password.request');
    Route::post('auth/forgot-password', [PasswordResetLinkController::class, 'store'])->name('auth.password.email');

    Route::get('auth/reset-password/{token}', [NewPasswordController::class, 'create'])->name('auth.password.reset');
    Route::post('auth/reset-password', [NewPasswordController::class, 'store'])->name('auth.password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('auth/verify-email', EmailVerificationPromptController::class)->name('auth.verification.notice');
    Route::get('auth/verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('auth.verification.verify');

    Route::post('auth/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('auth.verification.send');

    Route::get('auth/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('auth.password.confirm');
    Route::post('auth/confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('auth/password', [PasswordController::class, 'update'])->name('auth.password.update');

    Route::post('auth/logout', [AuthenticatedSessionController::class, 'destroy'])->name('auth.logout');
});
