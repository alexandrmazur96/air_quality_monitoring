<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('air_quality_records', static function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('city_id');
            $table->double('pm10');
            $table->double('pm2_5');
            $table->double('nh3');
            $table->double('o3');
            $table->double('no');
            $table->double('no2');
            $table->double('so2');
            $table->double('co');
            $table->unsignedTinyInteger('aqi');
            $table->timestamps();

            $table->index('created_at');
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('air_quality_records');
    }
};
