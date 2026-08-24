<?php

use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\ScraperController;
use Illuminate\Support\Facades\Route;

Route::post('/scrape', [ScraperController::class, 'scrape'])->name('api.scrape');
Route::get('/businesses', [ScraperController::class, 'businesses'])->name('api.businesses');
Route::get('/jobs/{id}', [ScraperController::class, 'job'])->name('api.jobs.show');

Route::get('/export/excel', [ExportController::class, 'excel'])->name('api.export.excel');
Route::get('/export/pdf', [ExportController::class, 'pdf'])->name('api.export.pdf');
Route::get('/export/image', [ExportController::class, 'image'])->name('api.export.image');