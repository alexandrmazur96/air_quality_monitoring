<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use Mazur\Models\City;

return new class extends Migration
{
    private const int MIN_POPULATION = 10_000;

    public function up(): void
    {
        DB::transaction(static function (): void {
            Reader::createFromPath(database_path('migrations/raw/ua_cities.csv'), 'rb')
                ->setHeaderOffset(0)
                ->each(static function (array $row): void {
                    if ($row['population'] < self::MIN_POPULATION) {
                        return;
                    }

                    City::query()->create([
                        'name' => $row['city'],
                        'country' => $row['iso2'],
                        'state' => $row['admin_name'],
                        'latitude' => $row['lat'],
                        'longitude' => $row['lng'],
                    ]);
                });
        });
    }

    public function down(): void
    {
        DB::table('cities')->delete();
    }
};
