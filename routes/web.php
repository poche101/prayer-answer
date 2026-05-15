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

// Redirect root route directly to the praise report submission view page
Route::get('/', function () {
    return view('welcome', ['type' => 'praise']);
});

// Using the 'welcome' view for both, but passing a 'type' variable
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
| We use 'submit-' in the URL to clearly distinguish these from the
| Admin Management Resource routes.
|
| NOTE: .withoutMiddleware() strips the CSRF token requirements so your
| stateless external API submits function seamlessly from your JavaScript.
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

    // The Dashboard View
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Logout Action
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Internal Dashboard APIs (Admin Management)
    |--------------------------------------------------------------------------
    | Explicitly overriding parameter bindings maps kebab-case route variables
    | directly to your Controller's camelCase parameters ($prayerReport / $praiseReport).
    */
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
    });
});
