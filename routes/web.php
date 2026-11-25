<?php

use App\Http\Controllers\PlaygroundController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'playground');

Route::prefix('api')->controller(PlaygroundController::class)->group(function () {
    Route::get('status', 'status');
    Route::get('products', 'products');
    Route::post('reset', 'reset');
    Route::post('run', 'run');
    Route::post('global', 'setGlobal');
});
