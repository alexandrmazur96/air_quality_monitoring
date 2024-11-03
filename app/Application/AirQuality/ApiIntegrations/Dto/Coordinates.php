<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\ApiIntegrations\Dto;

final readonly class Coordinates
{
    public function __construct(public float $latitude, public float $longitude)
    {
    }
}
