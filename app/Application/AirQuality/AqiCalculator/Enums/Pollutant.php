<?php

namespace App\Application\AirQuality\AqiCalculator\Enums;

enum Pollutant: string
{
    // Coarse particles matter
    case PM10 = 'pm10';
    // Fine particles matter
    case PM2_5 = 'pm2.5';
    // Ozone
    case O3 = 'o3';
    case NO2 = 'no2';
    // Nitrogen monoxide
    case NO = 'no';
    // Sulfur dioxide
    case SO2 = 'so2';
    // Carbon monoxide
    case CO = 'co';
    // Ammonia
    case NH3 = 'nh3';
}
