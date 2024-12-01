<?php

declare(strict_types=1);

namespace Mazur\Application\Converter;

use InvalidArgumentException;
use Mazur\Application\AirQuality\AqiCalculator\Enums\Pollutant;

final class ConcentrationConverter
{
    private const float MOLECULAR_WEIGHT_CO = 28.01;
    private const float MOLECULAR_WEIGHT_NO2 = 46.0055;
    private const float MOLECULAR_WEIGHT_SO2 = 64.066;
    private const float MOLECULAR_WEIGHT_O3 = 48.0;

    private const float MOLAR_VOLUME_CO = 22.4;
    private const float MOLAR_VOLUME_NO2 = 22.45;
    private const float MOLAR_VOLUME_SO2 = 21.89;
    private const float MOLAR_VOLUME_O3 = 21.6;

    public function microgramsPerCubicMeterToPpm(Pollutant $pollutant, float $concentration): float
    {
        return match ($pollutant) {
            Pollutant::CO => ($concentration * self::MOLAR_VOLUME_CO) / self::MOLECULAR_WEIGHT_CO * 1000,
            Pollutant::SO2 => ($concentration * self::MOLAR_VOLUME_SO2) / self::MOLECULAR_WEIGHT_SO2 * 1000,
            Pollutant::O3 => ($concentration * self::MOLAR_VOLUME_O3) / self::MOLECULAR_WEIGHT_O3 * 1000,
            Pollutant::NO2 => ($concentration * self::MOLAR_VOLUME_NO2) / self::MOLECULAR_WEIGHT_NO2 * 1000,
            default => throw new InvalidArgumentException('Pollutant not supported: ' . $pollutant->value),
        };
    }

    public function microgramsPerCubitMeterToPpb(Pollutant $pollutant, float $concentration): float
    {
        return $this->microgramsPerCubicMeterToPpm($pollutant, $concentration) * 1000;
    }
}
