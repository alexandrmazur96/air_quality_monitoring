<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\SourceUnion;

use Illuminate\Support\Collection;
use Mazur\Application\AirQuality\Entity\MapAirQuality;

final class MaxSelectionSourceUnion implements SourceUnionInterface
{
    /** @inheritdoc */
    public function uniteRaws(Collection $source1, Collection $source2): Collection
    {
        $united = new Collection();

        foreach ($source1 as $source1AirQuality) {
            $source2AirQuality = $source2->firstWhere(
                static fn(object $airQualityRaw): bool => $airQualityRaw->city_id === $source1AirQuality->city_id
            );
            if ($source2AirQuality === null) {
                $united->push(
                    new MapAirQuality(
                        cityId: $source1AirQuality->city_id,
                        co: $source1AirQuality->co,
                        no: $source1AirQuality->no,
                        no2: $source1AirQuality->no2,
                        o3: $source1AirQuality->o3,
                        so2: $source1AirQuality->so2,
                        pm2_5: $source1AirQuality->pm2_5,
                        pm10: $source1AirQuality->pm10,
                        nh3: $source1AirQuality->nh3,
                        provider: $source1AirQuality->provider,
                        aqiUk: (int)$source1AirQuality->aqi_uk,
                        aqiUs: (int)$source1AirQuality->aqi_us,
                        aqiEu: (int)$source1AirQuality->aqi_eu,
                        latitude: (float)$source1AirQuality->latitude,
                        longitude: (float)$source1AirQuality->longitude,
                        createdAt: $source1AirQuality->created_at . ' UTC',
                    )
                );
                continue;
            }

            $pm10 = max($source1AirQuality->pm10, $source2AirQuality->pm10);
            $pm25 = max($source1AirQuality->pm2_5, $source2AirQuality->pm2_5);
            $nh3 = max($source1AirQuality->nh3, $source2AirQuality->nh3);
            $o3 = max($source1AirQuality->o3, $source2AirQuality->o3);
            $no = max($source1AirQuality->no, $source2AirQuality->no);
            $no2 = max($source1AirQuality->no2, $source2AirQuality->no2);
            $so2 = max($source1AirQuality->so2, $source2AirQuality->so2);
            $co = max($source1AirQuality->co, $source2AirQuality->co);
            $provider = $source1AirQuality->provider . ' & ' . $source2AirQuality->provider;

            $united->push(
                new MapAirQuality(
                    cityId: $source1AirQuality->city_id,
                    co: $co,
                    no: $no,
                    no2: $no2,
                    o3: $o3,
                    so2: $so2,
                    pm2_5: $pm25,
                    pm10: $pm10,
                    nh3: $nh3,
                    provider: $provider,
                    aqiUk: null,
                    aqiUs: null,
                    aqiEu: null,
                    latitude: (float)$source1AirQuality->latitude,
                    longitude: (float)$source1AirQuality->longitude,
                    createdAt: $source1AirQuality->created_at . ' UTC',
                )
            );
        }

        // in case if source2 has some cities that are not in source1
        foreach ($source2 as $source2AirQuality) {
            $source1AirQuality = $source1->firstWhere(
                static fn(object $airQualityRaw): bool => $airQualityRaw->city_id === $source2AirQuality->city_id
            );
            if ($source1AirQuality === null) {
                $united->push(
                    new MapAirQuality(
                        cityId: $source2AirQuality->city_id,
                        co: $source2AirQuality->co,
                        no: $source2AirQuality->no,
                        no2: $source2AirQuality->no2,
                        o3: $source2AirQuality->o3,
                        so2: $source2AirQuality->so2,
                        pm2_5: $source2AirQuality->pm2_5,
                        pm10: $source2AirQuality->pm10,
                        nh3: $source2AirQuality->nh3,
                        provider: $source2AirQuality->provider,
                        aqiUk: (int)$source2AirQuality->aqi_uk,
                        aqiUs: (int)$source2AirQuality->aqi_us,
                        aqiEu: (int)$source2AirQuality->aqi_eu,
                        latitude: (float)$source2AirQuality->latitude,
                        longitude: (float)$source2AirQuality->longitude,
                        createdAt: $source2AirQuality->created_at . ' UTC'
                    )
                );
            }
        }

        return $united;
    }
}
