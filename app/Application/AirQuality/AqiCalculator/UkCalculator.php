<?php

declare(strict_types=1);

namespace App\Application\AirQuality\AqiCalculator;

use App\Application\AirQuality\AqiCalculator\Enums\Pollutant;
use App\Application\AirQuality\AqiCalculator\Exceptions\PollutantNotFoundException;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;

final class UkCalculator implements CalculatorInterface
{
    /*
     * UK Air Quality Index Levels Scale
     *
     * +----------------+-------+-------------------------------------------------+
     * | Qualitative    | Index | Pollutant concentration in µg/m³                |
     * | name           |       | SO₂     | NO₂     | PM₂.₅   | PM₁₀    | O₃      |
     * +----------------+-------+---------+---------+---------+---------+---------+
     * | Low            | 1     | 0–88    | 0–67    | 0–11    | 0–16    | 0–33    |
     * | Low            | 2     | 89–177  | 68–134  | 12–23   | 17–33   | 34–66   |
     * | Low            | 3     | 178–266 | 135–200 | 24–35   | 34–50   | 67–100  |
     * | Moderate       | 4     | 267–354 | 201–267 | 36–41   | 52–58   | 101–120 |
     * | Moderate       | 5     | 355–443 | 268–334 | 42–47   | 59–66   | 121–140 |
     * | Moderate       | 6     | 444–532 | 335–400 | 48–53   | 67–75   | 141–160 |
     * | High           | 7     | 533–710 | 401–467 | 54–58   | 76–83   | 161–187 |
     * | High           | 8     | 711–887 | 468–534 | 59–64   | 84–91   | 188–213 |
     * | High           | 9     | 888–1064| 535–600 | 65–70   | 92–100  | 214–240 |
     * | Very High      | 10    | ≥1065   | ≥601    | ≥71     | ≥101    | ≥241    |
     * +----------------+-------+---------+---------+---------+---------+---------+
     */

    private const array SCALE = [
        Pollutant::SO2->value => [
            [1, 0, 88],
            [2, 89, 177],
            [3, 178, 266],
            [4, 267, 354],
            [5, 355, 443],
            [6, 444, 532],
            [7, 533, 710],
            [8, 711, 887],
            [9, 888, 1064],
            [10, 1065, PHP_INT_MAX],
        ],
        Pollutant::NO2->value => [
            [1, 0, 67],
            [2, 68, 134],
            [3, 135, 200],
            [4, 201, 267],
            [5, 268, 334],
            [6, 335, 400],
            [7, 401, 467],
            [8, 468, 534],
            [9, 535, 600],
            [10, 601, PHP_INT_MAX],
        ],
        Pollutant::PM2_5->value => [
            [1, 0, 11],
            [2, 12, 23],
            [3, 24, 35],
            [4, 36, 41],
            [5, 42, 47],
            [6, 48, 53],
            [7, 54, 58],
            [8, 59, 64],
            [9, 65, 70],
            [10, 71, PHP_INT_MAX],
        ],
        Pollutant::PM10->value => [
            [1, 0, 16],
            [2, 17, 33],
            [3, 34, 50],
            [4, 52, 58],
            [5, 59, 66],
            [6, 67, 75],
            [7, 76, 83],
            [8, 84, 91],
            [9, 92, 100],
            [10, 101, PHP_INT_MAX],
        ],
        Pollutant::O3->value => [
            [1, 0, 33],
            [2, 34, 66],
            [3, 67, 100],
            [4, 101, 120],
            [5, 121, 140],
            [6, 141, 160],
            [7, 161, 187],
            [8, 188, 213],
            [9, 214, 240],
            [10, 241, PHP_INT_MAX],
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
