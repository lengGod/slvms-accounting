<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('debtors', function (Blueprint $table) {
            $table->enum('initial_balance_type', ['pokok', 'bagi_hasil'])->default('pokok')->after('initial_balance');
        });
    }

    public function down()
    {
        Schema::table('debtors', function (Blueprint $table) {
            $table->dropColumn('initial_balance_type');
        });
    }
};
