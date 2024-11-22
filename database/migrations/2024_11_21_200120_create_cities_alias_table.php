<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities_alias', static function (Blueprint $table): void {
            $table->id();
            $table->string('alias')->index('alias_idx');
            $table->foreignId('city_id')->constrained('cities');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities_alias');
    }
};
