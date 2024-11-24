<?php

declare(strict_types=1);

namespace Mazur\Application\Repository\AirQuality;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;
use Mazur\Application\AirQuality\ApiIntegrations\Enum\Provider;
use Mazur\Application\AirQuality\AqiCalculator\AqiCalculator;
use Mazur\Application\AirQuality\AqiCalculator\Enums\AqiType;

/**
 * @psalm-type _AirQualityRecord = object{
 *     provider: string,
 *     pm10: float,
 *     pm2_5: float,
 *     nh3: float,
 *     o3: float,
 *     no: float,
 *     no2: float,
 *     so2: float,
 *     co: float,
 *     created_at: string,
 *     latitude: float,
 *     longitude: float,
 *     city_id: int,
 * }
 */
final readonly class AirQualityRepository
{
    private const array AIR_QUALITY_RECORD_FIELDS = [
        'provider',
        'pm10',
        'pm2_5',
        'nh3',
        'o3',
        'no',
        'no2',
        'so2',
        'co',
        'air_quality_records.created_at',
        'cities.latitude',
        'cities.longitude',
        'aqi_uk',
        'aqi_us',
        'aqi_eu',
        'air_quality_records.city_id'
    ];

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

    /** @return Collection<array-key, _AirQualityRecord> */
    public function getCurrentAirQualityIndexes(Provider $provider): Collection
    {
        return DB::table('air_quality_records')
            ->join('cities', 'air_quality_records.city_id', '=', 'cities.id')
            ->where('air_quality_records.latest', '=', true)
            ->where('air_quality_records.provider', '=', $provider->value)
            ->select(self::AIR_QUALITY_RECORD_FIELDS)
            ->get();
    }

    /** @return Collection<array-key, _AirQualityRecord> */
    public function getLatestAirQualityIndexForCity(int $cityId, Provider $provider): Collection
    {
        return DB::table('air_quality_records')
            ->join('cities', 'air_quality_records.city_id', '=', 'cities.id')
            ->where('city_id', '=', $cityId)
            ->where('provider', '=', $provider->value)
            ->where('latest', '=', true)
            ->select(self::AIR_QUALITY_RECORD_FIELDS)
            ->get();
    }
}
