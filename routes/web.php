<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\VanController;
use App\Http\Controllers\Web\AuthController;

// Public Pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

// Vans
Route::get('/vans', [VanController::class, 'index'])->name('vans.index');
Route::get('/vans/{van}', [VanController::class, 'show'])->name('vans.show');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

