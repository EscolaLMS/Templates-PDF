<?php

use EscolaLms\TemplatesPdf\Http\Controllers\FabricPdfController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/pdfs'], function () {
    Route::get('/', [FabricPdfController::class, 'index']);
    Route::get('/{id}', [FabricPdfController::class, 'show']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => 'api/admin/pdfs'], function () {
    Route::get('/', [FabricPdfController::class, 'admin']);
});
