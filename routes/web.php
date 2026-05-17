<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\PraiseReportController;
use App\Http\Controllers\Api\PrayerReportController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfTokens;

/*
|--------------------------------------------------------------------------
| Public Routes (Views)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome', ['type' => 'praise']);
})->name('home');

Route::get('/submit-praise-report', function () {
    return view('welcome', ['type' => 'praise']);
})->name('praise.report.submit');

Route::get('/submit-prayer-report', function () {
    return view('welcome', ['type' => 'prayer']);
})->name('prayer.report.submit');

/*
|--------------------------------------------------------------------------
| Public API Endpoints (Submissions)
|--------------------------------------------------------------------------
*/
Route::middleware('throttle:10,1')
    ->withoutMiddleware([ValidateCsrfTokens::class])
    ->group(function () {
        Route::post('/api/submit-praise', [PraiseReportController::class, 'store'])->name('public.praise.store');
        Route::post('/api/submit-prayer', [PrayerReportController::class, 'store'])->name('public.prayer.store');
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Protected Admin Routes (Auth Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('api-v1')->group(function () {

        Route::apiResource('prayer-reports', PrayerReportController::class)
            ->except(['store'])
            ->parameters(['prayer-reports' => 'prayerReport']);

        Route::apiResource('praise-reports', PraiseReportController::class)
            ->except(['store'])
            ->parameters(['praise-reports' => 'praiseReport']);

        Route::get('/user', function (Illuminate\Http\Request $request) {
            return $request->user();
        });

    }); // closes prefix('api-v1')

}); // closes middleware(['auth'])
