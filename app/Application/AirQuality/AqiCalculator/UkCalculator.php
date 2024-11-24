<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\AqiCalculator;

use Mazur\Application\AirQuality\AqiCalculator\Enums\Pollutant;
use Mazur\Application\AirQuality\AqiCalculator\Exceptions\PollutantNotFoundException;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;
use Mazur\Application\AirQuality\Entity\IndexStringRepresentation;

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
            [2, 88.000001, 177],
            [3, 177.000001, 266],
            [4, 266.000001, 354],
            [5, 354.000001, 443],
            [6, 443.000001, 532],
            [7, 532.000001, 710],
            [8, 710.000001, 887],
            [9, 887.000001, 1064],
            [10, 1064.000001, PHP_INT_MAX],
        ],
        Pollutant::NO2->value => [
            [1, 0, 67],
            [2, 67.000001, 134],
            [3, 134.000001, 200],
            [4, 200.000001, 267],
            [5, 267.000001, 334],
            [6, 334.000001, 400],
            [7, 400.000001, 467],
            [8, 467.000001, 534],
            [9, 534.000001, 600],
            [10, 600.000001, PHP_INT_MAX],
        ],
        Pollutant::PM2_5->value => [
            [1, 0, 11],
            [2, 11.000001, 23],
            [3, 23.000001, 35],
            [4, 35.000001, 41],
            [5, 41.000001, 47],
            [6, 47.000001, 53],
            [7, 53.000001, 58],
            [8, 58.000001, 64],
            [9, 64.000001, 70],
            [10, 70.000001, PHP_INT_MAX],
        ],
        Pollutant::PM10->value => [
            [1, 0, 16],
            [2, 16.000001, 33],
            [3, 33.000001, 50],
            [4, 50.000001, 58],
            [5, 58.000001, 66],
            [6, 66.000001, 75],
            [7, 75.000001, 83],
            [8, 83.000001, 91],
            [9, 91.000001, 100],
            [10, 100.000001, PHP_INT_MAX],
        ],
        Pollutant::O3->value => [
            [1, 0, 33],
            [2, 33.000001, 66],
            [3, 66.000001, 100],
            [4, 100.000001, 120],
            [5, 120.000001, 140],
            [6, 140.000001, 160],
            [7, 160.000001, 187],
            [8, 187.000001, 213],
            [9, 213.000001, 240],
            [10, 240.000001, PHP_INT_MAX],
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

        throw new PollutantNotFoundException('Pollutant ' . $pollutant->value . ' concentration not found in the scale: ' . $concentration);
    }

    public function getStringRepresentation(int $index): IndexStringRepresentation
    {
        $indexStr = match ($index) {
            1, 2, 3 => 'Good',
            4, 5, 6 => 'Moderate',
            7, 8, 9 => 'Bad',
            10 => 'Very Bad',
            default => 'Unknown',
        };

        $description = match ($index) {
            1, 2, 3 => 'Enjoy your usual outdoor activities.',
            4, 5, 6 => 'Adults and children with lung problems, and adults with heart problems, who experience symptoms, should consider reducing strenuous physical activity, particularly outdoors.',
            7, 8, 9 => 'Adults and children with lung problems, and adults with heart problems, should reduce strenuous physical exertion, particularly outdoors, and particularly if they experience symptoms. People with asthma may find they need to use their reliever inhaler more often. Older people should also reduce physical exertion.',
            10 => 'Adults and children with lung problems, adults with heart problems, and older people, should avoid strenuous physical activity. People with asthma may find they need to use their reliever inhaler more often.',
            default => 'Unknown',
        };

        return new IndexStringRepresentation($indexStr, $description);
    }
}
