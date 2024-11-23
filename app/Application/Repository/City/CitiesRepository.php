<?php

declare(strict_types=1);

namespace Mazur\Application\Repository\City;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function find(int $id): ?City
    {
        return City::query()->find($id);
    }

    public function findCity(string $city, float $latitude, float $longitude): ?City
    {
        $cityObj = $this->findByCoords($latitude, $longitude);
        if ($cityObj === null) {
            Log::critical('Unable to find corresponding city for coordinates', [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            $cityObj = $this->findByCity($city);
            if ($cityObj === null) {
                Log::critical('Unable to find corresponding city for name', ['name' => $city]);

                return null;
            }
        }

        return $cityObj;
    }

    public function findByCity(string $city): ?City
    {
        $cityObj = City::query()->where('name', '=', $city)->first();
        if ($cityObj === null) {
            $cityId = DB::table('cities_alias')
                ->where('alias', '=', $city)
                ->limit(1)
                ->value('city_id');

            return City::find($cityId);
        }

        return $cityObj;
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
