<?php

use Illuminate\Support\Facades\Route;
use Mazur\Http\Controllers\AboutController;
use Mazur\Http\Controllers\AirQualityMapController;
use Mazur\Http\Controllers\CitiesController;

Route::get('/', [AirQualityMapController::class, 'index']);
Route::get('/current-air-quality-indexes', [AirQualityMapController::class, 'getCurrentAirQualityIndexes']);

Route::get('/supported-cities', [CitiesController::class, 'supportedCities']);
Route::get('/city/{city}', [CitiesController::class, 'cityDetails']);

Route::get('/about', [AboutController::class, 'about']);
Route::get('/aqi-us', [AboutController::class, 'aqiUs']);
Route::get('/aqi-uk', [AboutController::class, 'aqiUk']);
Route::get('/aqi-eu', [AboutController::class, 'aqiEu']);
