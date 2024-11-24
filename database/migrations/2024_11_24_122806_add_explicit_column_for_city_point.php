<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', static function (Blueprint $table): void {
            $table->geometry('location')->nullable();
        });

        foreach (DB::table('cities')->get(['id', 'latitude', 'longitude']) as $city) {
            DB::table('cities')
                ->where('id', $city->id)
                ->update([
                    'location' => DB::raw("POINT({$city->longitude}, {$city->latitude})"),
                ]);
        }

        Schema::table('cities', static function (Blueprint $table): void {
            $table->geometry('location')->nullable(false)->change();
            $table->spatialIndex('location', 'location_idx');
        });
    }

    public function down(): void
    {
        Schema::table('cities', static function (Blueprint $table): void {
            $table->dropSpatialIndex('location_idx');
            $table->dropColumn('location');
        });
    }
};
