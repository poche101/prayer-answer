<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PraiseReportController;

// This will be accessible at http://127.0.0.1:8000/api/praise-reports
Route::get('/praise-reports', [PraiseReportController::class, 'index']);
