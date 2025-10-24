<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('debtors', function (Blueprint $table) {
            // Ubah dari enum ke string agar bisa menyimpan kombinasi
            $table->string('initial_balance_type')->default('pokok')->after('initial_balance');
        });
    }

    public function down()
    {
        Schema::table('debtors', function (Blueprint $table) {
            $table->dropColumn('initial_balance_type');
        });
    }
};
