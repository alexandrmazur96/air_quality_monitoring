<?php

declare(strict_types=1);

namespace App\Application\AirQuality\AqiCalculator;

use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;

final class UsCalculator implements CalculatorInterface
{
    public function calculate(AirQuality $airQuality): int
    {

    }
}
