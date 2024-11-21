<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\ApiIntegrations\Enum;

enum Provider: string
{
    case OPEN_WEATHER = 'open_weather';
    case WEATHER_API = 'weather_api';
}
