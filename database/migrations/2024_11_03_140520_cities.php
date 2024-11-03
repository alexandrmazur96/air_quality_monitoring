<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', static function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->char('country', 2);
            $table->string('state');
            $table->double('latitude');
            $table->double('longitude');
            $table->timestamps();

            $table->index('name');
            $table->index('country');
            $table->index('state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};