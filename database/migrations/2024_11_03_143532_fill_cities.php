<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use Mazur\Models\City;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(static function (): void {
            // https://worldpopulationreview.com/cities/ukraine
            Reader::createFromPath(database_path('migrations/raw/ua_cities_v2.csv'), 'rb')
                ->setHeaderOffset(0)
                ->each(static function (array $row): void {
                    City::query()->create([
                        'name' => $row['city'],
                        'country' => 'UA',
                        'latitude' => $row['latitude'],
                        'longitude' => $row['longitude'],
                    ]);
                });
        });
    }

    public function down(): void
    {
        DB::table('cities')->delete();
    }
};
