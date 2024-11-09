<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\AqiCalculator;

use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;
use Mazur\Application\AirQuality\AqiCalculator\Enums\Pollutant;
use Mazur\Application\AirQuality\AqiCalculator\Exceptions\PollutantNotFoundException;

final class EuropeanCalculator implements CalculatorInterface
{
    /*
     * European Air Quality Index Levels (based on pollutant concentrations in µg/m³)
     *
     * +-----------------------------+----------------+------------+-----------+-----------+---------------+--------------------+
     * | Pollutant                   | Very Good (1)  | Good (2)   | Medium (3)| Poor (4)  | Very Poor (5) | Extremely Poor (6) |
     * +-----------------------------+----------------+------------+-----------+-----------+---------------+--------------------+
     * | Ozone (O₃)                  | 0-50           | 50-100     | 100-130   | 130-240   | 240-380       | 380-800            |
     * | Nitrogen Dioxide (NO₂)      | 0-40           | 40-90      | 90-120    | 120-230   | 230-340       | 340-1000           |
     * | Sulphur Dioxide (SO₂)       | 0-100          | 100-200    | 200-350   | 350-500   | 500-750       | 750-1250           |
     * | Particulate Matter (PM₁₀)   | 0-20           | 20-40      | 40-50     | 50-100    | 100-150       | 150-1200           |
     * | Particulate Matter (PM₂.₅)  | 0-10           | 10-20      | 20-25     | 25-50     | 50-75         | 75-800             |
     * +-----------------------------+----------------+------------+-----------+-----------+---------------+--------------------+
     */
    private const array SCALE = [
        Pollutant::O3->value => [
            [1, 0, 50],
            [2, 50, 100],
            [3, 100, 130],
            [4, 130, 240],
            [5, 240, 380],
            [6, 380, 800],
        ],
        Pollutant::NO2->value => [
            [1, 0, 40],
            [2, 40, 90],
            [3, 90, 120],
            [4, 120, 230],
            [5, 230, 340],
            [6, 340, 1000],
        ],
        Pollutant::SO2->value => [
            [1, 0, 100],
            [2, 100, 200],
            [3, 200, 350],
            [4, 350, 500],
            [5, 500, 750],
            [6, 750, 1250],
        ],
        Pollutant::PM10->value => [
            [1, 0, 20],
            [2, 20, 40],
            [3, 40, 50],
            [4, 50, 100],
            [5, 100, 150],
            [6, 150, 1200],
        ],
        Pollutant::PM2_5->value => [
            [1, 0, 10],
            [2, 10, 20],
            [3, 20, 25],
            [4, 25, 50],
            [5, 50, 75],
            [6, 75, 800],
        ],
    ];

    /** @throws PollutantNotFoundException */
    public function calculate(AirQuality $airQuality): int
    {
        return max(
            $this->calculateAqiForPollutant($airQuality->so2, Pollutant::SO2),
            $this->calculateAqiForPollutant($airQuality->no2, Pollutant::NO2),
            $this->calculateAqiForPollutant($airQuality->pm2_5, Pollutant::PM2_5),
            $this->calculateAqiForPollutant($airQuality->pm10, Pollutant::PM10),
            $this->calculateAqiForPollutant($airQuality->o3, Pollutant::O3),
        );
    }

    /** @throws PollutantNotFoundException */
    private function calculateAqiForPollutant(float $concentration, Pollutant $pollutant): int
    {
        if (!array_key_exists($pollutant->value, self::SCALE)) {
            throw new PollutantNotFoundException('Pollutant not found: ' . $pollutant->name);
        }

        $scale = self::SCALE[$pollutant->value];
        foreach ($scale as $item) {
            [$index, $min, $max] = $item;
            if ($concentration >= $min && $concentration <= $max) {
                return $index;
            }
        }

        throw new PollutantNotFoundException('Pollutant concentration not found in the scale: ' . $concentration);
    }
}
