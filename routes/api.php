<?php

use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserReportController;
use Illuminate\Support\Facades\Route;

// Device routes
Route::post('/device/check', [DeviceController::class, 'check']);
Route::post('/device/register', [DeviceController::class, 'register']);

// Message routes
Route::post('/messages/send', [MessageController::class, 'send']);
Route::get('/messages/random', [MessageController::class, 'random']);
Route::post('/messages/reply', [MessageController::class, 'sendReply']);
Route::get('/messages/all', [MessageController::class, 'all']);

// Report routes
Route::post('/report', [ReportController::class, 'store']);
Route::get('/report/check', [ReportController::class, 'check']);

Route::get('/user/ban-status', [UserReportController::class, 'checkBanStatus']);
