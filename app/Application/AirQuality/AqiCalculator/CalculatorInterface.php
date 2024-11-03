<?php

namespace App\Application\AirQuality\AqiCalculator;

use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;

interface CalculatorInterface
{
    public function calculate(AirQuality $airQuality): int;
}
