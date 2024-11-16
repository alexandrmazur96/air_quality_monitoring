<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('air_quality_records', static function (Blueprint $table): void {
            $table->string('provider')->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('air_quality_records', static function (Blueprint $table): void {
            $table->dropColumn('provider');
        });
    }
};
