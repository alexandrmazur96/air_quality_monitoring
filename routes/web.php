<?php

use Illuminate\Support\Facades\Route;
use Mazur\Http\Controllers\AirQualityMapController;

Route::get('/', [AirQualityMapController::class, 'index']);
Route::get('/current-air-quality-indexes', [AirQualityMapController::class, 'getCurrentAirQualityIndexes']);
