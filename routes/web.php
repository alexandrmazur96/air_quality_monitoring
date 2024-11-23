<?php

use Illuminate\Support\Facades\Route;
use Mazur\Http\Controllers\AboutController;
use Mazur\Http\Controllers\AirQualityMapController;

Route::get('/', [AirQualityMapController::class, 'index']);
Route::get('/current-air-quality-indexes', [AirQualityMapController::class, 'getCurrentAirQualityIndexes']);
Route::get('/about', [AboutController::class, 'about']);
Route::get('/supported-cities', [AboutController::class, 'supportedCities']);
Route::get('/aqi-us', [AboutController::class, 'aqiUs']);
Route::get('/aqi-uk', [AboutController::class, 'aqiUk']);
Route::get('/aqi-eu', [AboutController::class, 'aqiEu']);
