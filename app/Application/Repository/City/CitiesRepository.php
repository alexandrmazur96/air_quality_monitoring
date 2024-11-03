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
}
