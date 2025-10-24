<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->json('filters')->nullable();
            $table->string('generated_by')->nullable();
            $table->timestamp('generated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['type', 'filters', 'generated_by', 'generated_at']);
        });
    }
};
