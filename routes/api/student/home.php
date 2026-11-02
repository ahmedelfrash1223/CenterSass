<?php

use App\Http\Controllers\Api\Student\Home\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [HomeController::class, 'index'])->name('home');
