<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/public/projects', [PublicController::class, 'apiProjects']);
Route::get('/public/projects/{project:slug}', [PublicController::class, 'apiProject']);
Route::get('/public/projects/{project:slug}/images/{image}', [PublicController::class, 'projectImage']);
Route::get('/public/projects/{project:slug}/report', [PublicController::class, 'projectReport']);
Route::post('/public/submissions', [ProjectController::class, 'store'])->middleware('throttle:5,10');
