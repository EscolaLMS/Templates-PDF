<?php

use EscolaLms\TemplatesPdf\Http\Controllers\FabricPdfController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/pdfs'], function () {
    Route::put('/reportbro/report/run', [FabricPdfController::class, 'reportBro']);
    Route::get('/', [FabricPdfController::class, 'index']);
    Route::get('/generate/{id}', [FabricPdfController::class, 'generate']);
    Route::get('/{id}', [FabricPdfController::class, 'show']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => 'api/admin/pdfs'], function () {
    Route::get('/', [FabricPdfController::class, 'admin']);
});
