<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\Entity;

use JsonSerializable;
use Mazur\Application\AirQuality\ApiIntegrations\Dto\AirQuality;

/**
 * @psalm-type _MapAirQuality=array{
 *      city_id: int,
 *      co: float,
 *      no: float,
 *      no2: float,
 *      o3: float,
 *      so2: float,
 *      pm2_5: float,
 *      pm10: float,
 *      nh3: float,
 *      provider: string,
 *      aqi_uk: ?int,
 *      aqi_us: ?int,
 *      aqi_eu: ?int,
 *      latitude: float,
 *      longitude: float,
 *      created_at: string,
 *  }
 */
final readonly class MapAirQuality implements JsonSerializable
{
    public function __construct(
        public int $cityId,
        public float $co,
        public float $no,
        public float $no2,
        public float $o3,
        public float $so2,
        public float $pm2_5,
        public float $pm10,
        public float $nh3,
        public string $provider,
        public ?int $aqiUk,
        public ?int $aqiUs,
        public ?int $aqiEu,
        public float $latitude,
        public float $longitude,
        public string $createdAt
    ) {
    }

    public function withAqiIndexes(int $aqiUk, int $aqiUs, int $aqiEu): self
    {
        return new self(
            $this->cityId,
            $this->co,
            $this->no,
            $this->no2,
            $this->o3,
            $this->so2,
            $this->pm2_5,
            $this->pm10,
            $this->nh3,
            $this->provider,
            $aqiUk,
            $aqiUs,
            $aqiEu,
            $this->latitude,
            $this->longitude,
            $this->createdAt
        );
    }

    public function toAirQuality(): AirQuality
    {
        return new AirQuality(
            cityId: $this->cityId,
            co: $this->co,
            no: $this->no,
            no2: $this->no2,
            o3: $this->o3,
            so2: $this->so2,
            pm2_5: $this->pm2_5,
            pm10: $this->pm10,
            nh3: $this->nh3
        );
    }

    /** @return _MapAirQuality */
    public function jsonSerialize(): array
    {
        return [
            'city_id' => $this->cityId,
            'co' => $this->co,
            'no' => $this->no,
            'no2' => $this->no2,
            'o3' => $this->o3,
            'so2' => $this->so2,
            'pm2_5' => $this->pm2_5,
            'pm10' => $this->pm10,
            'nh3' => $this->nh3,
            'provider' => $this->provider,
            'aqi_uk' => $this->aqiUk,
            'aqi_us' => $this->aqiUs,
            'aqi_eu' => $this->aqiEu,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->createdAt,
        ];
    }
}
