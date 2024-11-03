<?php

declare(strict_types=1);

namespace App\Application\AirQuality\AqiCalculator\Enums;

enum AqiType: string
{
    case UK = 'uk';
    case EUROPE = 'europe';
    case US = 'us';
    case CHINA = 'china';
}
