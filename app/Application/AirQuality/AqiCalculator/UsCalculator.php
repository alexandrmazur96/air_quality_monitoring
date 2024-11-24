<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\AqiCalculator;

use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;
use Mazur\Application\AirQuality\AqiCalculator\Enums\Pollutant;
use Mazur\Application\AirQuality\AqiCalculator\Exceptions\PollutantNotFoundException;
use Mazur\Application\AirQuality\Entity\IndexStringRepresentation;
use Mazur\Application\Converter\ConcentrationConverter;

final class UsCalculator implements CalculatorInterface
{
    /**
     * U.S. Air Quality Index (AQI) Breakpoints Table
     * |---------------------------------|-----------|------------------------|----------------|---------------|----------|-----------------|----------------|
     * | AQI Category                    | AQI Range | Ozone (O₃), ppm        | PM₂.₅ (µg/m³)  | PM₁₀ (µg/m³)  | CO (ppm) | SO₂ (ppb)       | NO₂ (ppb)      |
     * |---------------------------------|-----------|------------------------|----------------|---------------|----------|-----------------|----------------|
     * | Good                            | 0-50      | 0.000-0.054 (1)        | 0.0-9.0        | 0-54          | 0.0-4.4  | 0-35            | 0-53           |
     * | Moderate                        | 51-100    | 0.055-0.124 (1)        | 9.1-35.4       | 55-154        | 4.5-9.4  | 36-75           | 54-100         |
     * | Unhealthy for Sensitive Groups  | 101-150   | 0.125-0.164            | 35.5-55.4      | 155-254       | 9.5-12.4 | 76-185          | 101-360        |
     * | Unhealthy                       | 151-200   | 0.165-0.204            | 55.5-125.4     | 255-354       | 12.5-15.4| - (2)           | 361-649        |
     * | Very Unhealthy                  | 201-300   | 0.205-0.404            | 125.5-225.4    | 355-424       | 15.5-30.4| - (2)           | 650-1249       |
     * | Hazardous                       | 301+      | >=0.405                | >=225.5        | >=425         | >=30.5   | - (2)           | >=1250         |
     * |---------------------------------|-----------|------------------------|----------------|---------------|----------|-----------------|----------------|
     * (1) - Breakpoints from O₃ 8-hour concentrations, should be used to calculate index.
     * (2) - Do not defines higher AQI values
     * Ip = (Ihi - Ilo) / (BPhi - BPlo) * (Cp - BPlo) + Ilo
     * Where:
     * Ip = the index for pollutant p
     * Cp = the truncated concentration of pollutant p
     * BPhi = the concentration breakpoint that is greater than or equal to Cp
     * BPlo = the concentration breakpoint that is less than or equal to Cp
     * Ihi = the AQI value corresponding to BPHi
     * Ilo = the AQI value corresponding to BPLo
     * Process:
     * 1. Identify the highest concentration among all of the monitors within
     * each reporting area and truncate as follows:
     * Ozone (ppm) – truncate to 3 decimal places
     * PM2.5 (µg/m3) – truncate to 1 decimal place
     * PM10 (µg/m3) – truncate to integer
     * CO (ppm) – truncate to 1 decimal place
     * SO2 (ppb) – truncate to integer
     * NO2 (ppb) – truncate to integer
     * 2. Using table, find the two breakpoints that contain the concentration.
     * 3. Using equation, calculate the index.
     * 4. Round the index to the nearest integer.
     * --- For AQI values in the hazardous category, AQI values greater than 500
     * should be calculated using equation 1 and the concentration specified for the AQI value of 500.
     * The AQI value of 500 are as follows:
     * O3: 0.604 ppm;
     * PM2.5: 325.4 μg/m3;
     * PM10: 604 μg/m3;
     * CO: 50.4 ppm;
     * SO2: 1004 ppb;
     * NO2: 2049 ppb;
     */

    private const array SCALE = [
        Pollutant::O3->value => [
            [0, 50, .0, .054],
            [51, 100, .055, .070],
            [101, 150, .125, .164],
            [151, 200, .165, .204],
            [201, 300, .205, .404],
            [301, 500, .405, .604],
        ],
        Pollutant::PM2_5->value => [
            [0, 50, .0, 9.0],
            [51, 100, 9.1, 35.4],
            [101, 150, 35.5, 55.4],
            [151, 200, 55.5, 125.4],
            [201, 300, 125.5, 225.4],
            [301, 500, 225.5, 325.4],
        ],
        Pollutant::PM10->value => [
            [0, 50, 0, 54],
            [51, 100, 55, 154],
            [101, 150, 155, 254],
            [151, 200, 255, 354],
            [201, 300, 355, 424],
            [301, 500, 425, 604],
        ],
        Pollutant::CO->value => [
            [0, 50, .0, 4.4],
            [51, 100, 4.5, 9.4],
            [101, 150, 9.5, 12.4],
            [151, 200, 12.5, 15.4],
            [201, 300, 15.5, 30.4],
            [301, 500, 30.5, 50.4],
        ],
        Pollutant::SO2->value => [
            [0, 50, 0, 35],
            [51, 100, 36, 75],
            [101, 150, 76, 185],
            [151, 200, null, null],
            [201, 300, null, null],
            [301, 500, null, null],
        ],
        Pollutant::NO2->value => [
            [0, 50, 0, 53],
            [51, 100, 54, 100],
            [101, 150, 101, 360],
            [151, 200, 361, 649],
            [201, 300, 650, 1249],
            [301, 500, 1250, 2049],
        ],
    ];

    public function __construct(private readonly ConcentrationConverter $concentrationConverter)
    {
    }

    /** @throws PollutantNotFoundException */
    public function calculate(AirQuality $airQuality): int
    {
        return (int)round(
            max(
                ...array_filter([
                    $this->calculateAqiForPollutant(
                        (int)$this->concentrationConverter->microgramsPerCubitMeterToPpb(Pollutant::SO2, $airQuality->so2),
                        Pollutant::SO2
                    ),
                    $this->calculateAqiForPollutant(
                        (int)$this->concentrationConverter->microgramsPerCubitMeterToPpb(Pollutant::NO2, $airQuality->no2),
                        Pollutant::NO2
                    ),
                    $this->calculateAqiForPollutant(floor($airQuality->pm2_5 * 10) / 10, Pollutant::PM2_5),
                    $this->calculateAqiForPollutant((int)$airQuality->pm10, Pollutant::PM10),
                    $this->calculateAqiForPollutant(
                        floor(
                            $this->concentrationConverter->microgramsPerCubicMeterToPpm(Pollutant::O3, $airQuality->o3)
                            * 1000
                        ) / 1000,
                        Pollutant::O3
                    ),
                    $this->calculateAqiForPollutant(
                        floor(
                            $this->concentrationConverter->microgramsPerCubicMeterToPpm(Pollutant::CO, $airQuality->co)
                            * 10
                        ) / 10,
                        Pollutant::CO
                    ),
                ])
            )
        );
    }

    private function calculateAqiForPollutant(float $concentration, Pollutant $pollutant): ?float
    {
        if (!array_key_exists($pollutant->value, self::SCALE)) {
            throw new PollutantNotFoundException('Pollutant not found: ' . $pollutant->name);
        }

        $scale = self::SCALE[$pollutant->value];
        $Ihi = 500;
        $Ilo = 301;
        $BPlo = null;
        $BPhi = null;
        foreach ($scale as $row) {
            [$Ihi, $Ilo, $BPlo, $BPhi] = $row;
            if ($BPlo === null || $BPhi === null) {
                // AQI can not be calculated for this pollutant
                return null;
            }

            if ($concentration >= $BPlo && $concentration <= $BPhi) {
                break;
            }
        }

        return ($Ihi - $Ilo) / ($BPhi - $BPlo) * ($concentration - $BPlo) + $Ilo;
    }

    public function getStringRepresentation(int $index): IndexStringRepresentation
    {
        $indexStr = match (true) {
            $index >= 0 && $index <= 50 => 'Good',
            $index >= 51 && $index <= 100 => 'Moderate',
            $index >= 101 && $index <= 150 => 'Unhealthy for Sensitive Groups',
            $index >= 151 && $index <= 200 => 'Unhealthy',
            $index >= 201 && $index <= 300 => 'Very Unhealthy',
            $index >= 301 => 'Hazardous',
            default => 'Unknown',
        };

        $description = match (true) {
            $index >= 0 && $index <= 50 => 'Air quality is considered satisfactory, and air pollution poses little or no risk.',
            $index >= 51 && $index <= 100 => 'Air quality is acceptable. However, there may be a risk for some people, particularly those who are unusually sensitive to air pollution.',
            $index >= 101 && $index <= 150 => 'Members of sensitive groups may experience health effects. The general public is less likely to be affected.',
            $index >= 151 && $index <= 200 => 'Some members of the general public may experience health effects; members of sensitive groups may experience more serious health effects.',
            $index >= 201 && $index <= 300 => 'Health alert: The risk of health effects is increased for everyone.',
            $index >= 301 => 'Health warning of emergency conditions: everyone is more likely to be affected.',
            default => 'Unknown',
        };

        return new IndexStringRepresentation($indexStr, $description);
    }
}
