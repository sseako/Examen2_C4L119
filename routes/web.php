<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservaController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
