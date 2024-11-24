<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_location', static function (Blueprint $table): void {
            $table->id();

            $table->string('chat_id');
            $table->geometry('location');
            $table->float('latitude', 10, 6);
            $table->float('longitude', 10, 6);
            $table->foreignId('nearest_city_id')->constrained('cities');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_location');
    }
};
