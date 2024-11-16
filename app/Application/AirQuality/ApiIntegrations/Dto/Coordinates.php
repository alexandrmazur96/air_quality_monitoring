<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\ApiIntegrations\Dto;

final readonly class Coordinates
{
    public function __construct(public string $latitude, public string $longitude)
    {
    }
}
