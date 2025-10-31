<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('titipans', function (Blueprint $table) {
            $table->decimal('bagi_pokok', 15, 2)->default(0)->after('amount');
            $table->decimal('bagi_hasil', 15, 2)->default(0)->after('bagi_pokok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('titipans', function (Blueprint $table) {
            $table->dropColumn(['bagi_pokok', 'bagi_hasil']);
        });
    }
};