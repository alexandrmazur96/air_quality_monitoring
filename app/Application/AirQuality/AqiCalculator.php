<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality;

use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;
use Mazur\Application\AirQuality\Enums\AqiType;
use Mazur\Application\AirQuality\Exceptions\UnknownAqiIndexTypeException;

final class AqiCalculator
{
    public function calculate(AirQuality $airQuality, AqiType $aqiType): int
    {
        return match ($aqiType) {
            AqiType::UK => $this->calculateUkAqi($airQuality),
            AqiType::EUROPE => $this->calculateEuropeAqi($airQuality),
            AqiType::US => $this->calculateUsAqi($airQuality),
            AqiType::CHINA => $this->calculateChinaAqi($airQuality),
            default => throw new UnknownAqiIndexTypeException(),
        };
    }
}
