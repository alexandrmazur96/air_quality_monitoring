<?php

namespace Mazur\Application\AirQuality\AqiCalculator;

use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;
use Mazur\Application\AirQuality\AqiCalculator\Exceptions\PollutantNotFoundException;

interface CalculatorInterface
{
    /** @throws PollutantNotFoundException */
    public function calculate(AirQuality $airQuality): int;
}
