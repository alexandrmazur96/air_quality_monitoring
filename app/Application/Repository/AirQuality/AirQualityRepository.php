<?php

declare(strict_types=1);

namespace Mazur\Application\Repository\AirQuality;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;
use Mazur\Application\AirQuality\ApiIntegrations\Enum\Provider;
use Mazur\Application\AirQuality\AqiCalculator\AqiCalculator;
use Mazur\Application\AirQuality\AqiCalculator\Enums\AqiType;

final class AirQualityRepository
{
    public function __construct(private AqiCalculator $aqiCalculator)
    {
    }

    public function markCurrentRecordsAsNonLatest(Provider $provider): void
    {
        DB::table('air_quality_records')
            ->where('provider', '=', $provider->value)
            ->update(['latest' => false]);
    }

    /** @param iterable<array-key, AirQuality> $airQualityRecords */
    public function create(Provider $provider, iterable $airQualityRecords): void
    {
        DB::transaction(function () use ($airQualityRecords, $provider): void {
            foreach ($airQualityRecords as $airQualityRecord) {
                $aqiUk = $this->aqiCalculator->calculate($airQualityRecord, AqiType::UK);
                $aqiUs = $this->aqiCalculator->calculate($airQualityRecord, AqiType::US);
                $aqiEurope = $this->aqiCalculator->calculate($airQualityRecord, AqiType::EUROPE);
                $now = now();
                DB::table('air_quality_records')->insert([
                    'provider' => $provider->value,
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
                    'latest' => true,
                ]);
            }
        });
    }

    /** @return Collection<array-key, object{provider:string, latitude: float, longitude: float, aqi_uk: int, aqi_us: int, aqi_eu: int} */
    public function getCurrentAirQualityIndexes(): Collection
    {
        return DB::table('air_quality_records')
            ->join('cities', 'air_quality_records.city_id', '=', 'cities.id')
            ->where('air_quality_records.latest', '=', true)
            ->select('provider', 'cities.latitude', 'cities.longitude', 'aqi_uk', 'aqi_us', 'aqi_eu')
            ->get();
    }
}
