<?php

use Illuminate\Support\Facades\Route;
use Mazur\Http\Controllers\AboutController;
use Mazur\Http\Controllers\AirQualityMapController;

Route::get('/', [AirQualityMapController::class, 'index']);
Route::get('/current-air-quality-indexes', [AirQualityMapController::class, 'getCurrentAirQualityIndexes']);
Route::get('/about', [AboutController::class, 'about']);
Route::get('/supported-cities', [AboutController::class, 'supportedCities']);
