<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('debtors', function (Blueprint $table) {
            $table->date('joined_at')->nullable()->after('phone');
            $table->string('category')->nullable()->after('joined_at');
        });
    }

    public function down(): void
    {
        Schema::table('debtors', function (Blueprint $table) {
            $table->dropColumn(['joined_at', 'category']);
        });
    }
};
