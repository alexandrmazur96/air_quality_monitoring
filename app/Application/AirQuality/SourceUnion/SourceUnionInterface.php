<?php

declare(strict_types=1);

namespace Mazur\Application\AirQuality\SourceUnion;

use Illuminate\Support\Collection;
use Mazur\Application\AirQuality\Entity\MapAirQuality;
use Mazur\Application\Repository\AirQuality\AirQualityRepository;

/**
 * @psalm-import-type _AirQualityRecord from AirQualityRepository
 *
 */
interface SourceUnionInterface
{
    /**
     * @param Collection<array-key, _AirQualityRecord> $source1
     * @param Collection<array-key, _AirQualityRecord> $source2
     * @return Collection<array-key, MapAirQuality>
     */
    public function uniteRaws(Collection $source1, Collection $source2): Collection;
}
