<?php

declare(strict_types=1);

namespace Mazur\Application\Repository\AirQuality;

use Illuminate\Support\Facades\DB;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;

final class AirQualityRepository
{
    /** @param iterable<array-key, AirQuality> $airQualityRecords */
    public function create(iterable $airQualityRecords): void
    {
        DB::transaction(static function () use ($airQualityRecords): void {
            foreach ($airQualityRecords as $airQualityRecord) {
                DB::table('air_quality')->insert([
                    'city_id' => $airQualityRecord->cityId,
                    'aqi' => $airQualityRecord->aqi,
                    'co' => $airQualityRecord->co,
                    'no' => $airQualityRecord->no,
                    'no2' => $airQualityRecord->no2,
                    'o3' => $airQualityRecord->o3,
                    'so2' => $airQualityRecord->so2,
                    'pm2_5' => $airQualityRecord->pm2_5,
                    'pm10' => $airQualityRecord->pm10,
                    'nh3' => $airQualityRecord->nh3,
                ]);
            }
        });
    }
}
