<?php

declare(strict_types=1);

namespace Mazur\Application\Repository\AirQuality;

use Illuminate\Support\Facades\DB;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;
use Mazur\Application\AirQuality\AqiCalculator\AqiCalculator;
use Mazur\Application\AirQuality\AqiCalculator\Enums\AqiType;

final class AirQualityRepository
{
    public function __construct(private AqiCalculator $aqiCalculator)
    {
    }

    /** @param iterable<array-key, AirQuality> $airQualityRecords */
    public function create(iterable $airQualityRecords): void
    {
        DB::transaction(function () use ($airQualityRecords): void {
            foreach ($airQualityRecords as $airQualityRecord) {
                $aqiUk = $this->aqiCalculator->calculate($airQualityRecord, AqiType::UK);
                $aqiUs = $this->aqiCalculator->calculate($airQualityRecord, AqiType::US);
                $aqiEurope = $this->aqiCalculator->calculate($airQualityRecord, AqiType::EUROPE);
                $now = now();
                DB::table('air_quality_records')->insert([
                    'city_id' => $airQualityRecord->cityId,
                    'aqi_uk' => $aqiUk,
                    'aqi_us' => $aqiUs,
                    'aqi_eu' => $aqiEurope,
                    'co' => $airQualityRecord->co,
                    'no' => $airQualityRecord->no,
                    'no2' => $airQualityRecord->no2,
                    'o3' => $airQualityRecord->o3,
                    'so2' => $airQualityRecord->so2,
                    'pm2_5' => $airQualityRecord->pm2_5,
                    'pm10' => $airQualityRecord->pm10,
                    'nh3' => $airQualityRecord->nh3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }
}
