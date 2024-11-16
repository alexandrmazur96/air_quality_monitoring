<?php

declare(strict_types=1);

namespace Mazur\Application\Repository\City;

use Illuminate\Database\Eloquent\Collection;
use Mazur\Models\City;

final class CitiesRepository
{
    /**
     * @param list<string> $fields
     * @param list<string> $with
     * @return Collection<array-key, City>
     */
    public function all(
        array $fields = ['*'],
        array $with = []
    ): Collection {
        return City::query()->with($with)->get($fields);
    }

    public function findByCoords(float $latitude, float $longitude): ?City
    {
        $latStr = (string)$latitude;
        $lonStr = (string)$longitude;
        if (strlen($latStr) > 5) {
            $latStr = substr($latStr, 0, 5);
        }
        if (strlen($lonStr) > 5) {
            $lonStr = substr($lonStr, 0, 5);
        }

        /** @var City|null $city */
        return City::query()
            ->where('latitude', 'like', $latStr . '%')
            ->where('longitude', 'like', $lonStr . '%')
            ->first();
    }
}
