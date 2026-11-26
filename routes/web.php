<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SectionItemController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrivacyController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/privacy', PrivacyController::class)->name('privacy');
Route::post('/contact', [ContactMessageController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.store');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [SectionController::class, 'index'])->name('sections.index');
    Route::resource('sections', SectionController::class)->except(['index', 'show']);
    Route::patch('sections/{section}/visibility', [SectionController::class, 'toggleVisibility'])->name('sections.visibility');
    Route::patch('sections/{section}/move/{direction}', [SectionController::class, 'move'])
        ->whereIn('direction', ['up', 'down'])
        ->name('sections.move');

    Route::resource('sections.items', SectionItemController::class)->except(['index', 'show'])->scoped();
    Route::patch('sections/{section}/items/{item}/move/{direction}', [SectionItemController::class, 'move'])
        ->scopeBindings()
        ->whereIn('direction', ['up', 'down'])
        ->name('sections.items.move');

    Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('account', [AccountController::class, 'edit'])->name('account.edit');
    Route::get('account/two-factor', [AccountController::class, 'twoFactor'])
        ->middleware('password.confirm')
        ->name('account.two-factor');
});
