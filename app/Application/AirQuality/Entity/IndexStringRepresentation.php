<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\Entity;

final readonly class IndexStringRepresentation
{
    public function __construct(public string $index, public string $description)
    {
    }
}
