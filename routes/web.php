<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionCheckerTrait;

Route::get('/users', [UserController::class, 'index']);