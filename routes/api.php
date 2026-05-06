<?php

use App\Http\Controllers\Api\AttendeeController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// do middleware inside api.php so you won't have to bother with whatever the fuck the error is that controller class gives you, also just is easier 
// using except method for convenience, so every other method needs you to be logged in
Route::apiResource('events', EventController::class)->middleware('auth:sanctum')->except(['index', 'show']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('events.attendees', AttendeeController::class)->scoped()->except(['update']);
