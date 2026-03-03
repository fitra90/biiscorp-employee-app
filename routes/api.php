<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/employees',        [EmployeeController::class, 'apiList']);   
Route::post('/employees',       [EmployeeController::class, 'store']);
Route::get('/employees/table',  [EmployeeController::class, 'apiIndex']);