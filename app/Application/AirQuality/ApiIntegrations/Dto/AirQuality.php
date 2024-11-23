<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\ApiIntegrations\Dto;

use JsonSerializable;

/**
 * @psalm-type _AirQuality=array{
 *     city_id: int,
 *     co: float,
 *     no: float,
 *     no2: float,
 *     o3: float,
 *     so2: float,
 *     pm2_5: float,
 *     pm10: float,
 *     nh3: float
 * }
 */
final readonly class AirQuality implements JsonSerializable
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
        public float $nh3
    ) {
    }

    /** @return _AirQuality */
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
            'nh3' => $this->nh3
        ];
    }
}
