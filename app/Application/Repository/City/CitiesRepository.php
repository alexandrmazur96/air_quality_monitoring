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

    public function findByCity(string $city): ?City
    {
        return City::query()->where('name', '=', $city)->first();
    }

    public function findByCoords(float $latitude, float $longitude): ?City
    {
        $latStr = (string)$latitude;
        $lonStr = (string)$longitude;
        // try to find city by exact coordinates
        $city = City::query()
            ->where('latitude', 'like', $latStr . '%')
            ->where('longitude', 'like', $lonStr . '%')
            ->first();

        if ($city !== null) {
            return $city;
        }

        // if not found, try to find city by integer part of coordinates
        $lat = explode('.', $latStr)[0];
        $lon = explode('.', $lonStr)[0];

        $cities = City::query()
            ->where('latitude', 'like', $lat . '%')
            ->where('longitude', 'like', $lon . '%')
            ->get();

        if ($cities->count() > 1) {
            // if there are more than one city with the same integer part of coordinates
            // try to find city by first 5 digits of coordinates (two int, two decimal, one separator)
            $lat = $latStr;
            $lon = $lonStr;
            if (strlen($latStr) > 5) {
                $lat = substr($latStr, 0, 5);
            }
            if (strlen($lonStr) > 5) {
                $lon = substr($lonStr, 0, 5);
            }


            /** @var City|null $city */
            return City::query()
                ->where('latitude', 'like', $lat . '%')
                ->where('longitude', 'like', $lon . '%')
                ->first();
        }

        return $cities->first();
    }
}
