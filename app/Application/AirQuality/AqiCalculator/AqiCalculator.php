<?php

declare(strict_types=1);

namespace App\Application\AirQuality\AqiCalculator;

use App\Application\AirQuality\AqiCalculator\Enums\AqiType;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;

final class AqiCalculator
{
    public function calculate(AirQuality $airQuality, AqiType $aqiType): int
    {
        return match ($aqiType) {
            AqiType::UK => $this->calculateUkAqi($airQuality),
            AqiType::EUROPE => $this->calculateEuropeAqi($airQuality),
            AqiType::US => $this->calculateUsAqi($airQuality),
            AqiType::CHINA => $this->calculateChinaAqi($airQuality),
        };
    }

    private function calculateUkAqi(AirQuality $airQuality): int
    {
        // UK AQI calculation logic
    }

    private function calculateEuropeAqi(AirQuality $airQuality): int
    {
        // Europe AQI calculation logic
    }

    private function calculateUsAqi(AirQuality $airQuality): int
    {
        // US AQI calculation logic
    }

    private function calculateChinaAqi(AirQuality $airQuality)
    {
        // China AQI calculation logic
    }
}
