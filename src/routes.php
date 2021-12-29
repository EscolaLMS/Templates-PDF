<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {
    Route::middleware('auth:api')->prefix('admin')->group(function () {
    });
});
