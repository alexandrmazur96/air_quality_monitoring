<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\ApiIntegrations\Dto;

final readonly class AirQuality
{
    public function __construct(
        public int $cityId,
        public int $aqi,
        public float $co,
        public float $no,
        public float $no2,
        public float $o3,
        public float $so2,
        public float $pm2_5,
        public float $pm10,
        public float $nh3,
    ) {
    }
}
