<?php

declare(strict_types=1);

namespace Mazur\Application\Repository\User;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Mazur\Models\City;
use Mazur\Models\UserLocation;

final class UsersLocationRepository
{
    public function store(string $chatId, float $latitude, float $longitude): UserLocation
    {
        return UserLocation::updateOrCreate([
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location' => DB::raw("ST_GeomFromText('POINT($latitude $longitude)')"),
            'nearest_city_id' => $this->getNearestCity($latitude, $longitude)->id,
        ]);
    }

    /**
     * @param list<string> $select
     * @param list<string> $with
     * @return Collection<array-key, UserLocation>
     */
    public function getAllSubscribed(array $select = ['*'], array $with = []): Collection
    {
        return UserLocation::query()->with($with)->get($select);
    }

    private function getNearestCity(float $latitude, float $longitude): City
    {
        return City::selectRaw('id, name, ST_Distance(location, ST_GeomFromText(?)) as distance', ["POINT($longitude $latitude)"])
            ->orderBy('distance')
            ->limit(1)
            ->firstOrFail();
    }
}
