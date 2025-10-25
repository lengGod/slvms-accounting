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
            $table->decimal('initial_pokok_balance', 15, 2)->default(0)->after('initial_balance_type');
            $table->decimal('initial_bagi_hasil_balance', 15, 2)->default(0)->after('initial_pokok_balance');
        });
    }

    public function down()
    {
        Schema::table('debtors', function (Blueprint $table) {
            $table->dropColumn(['initial_balance_type', 'initial_pokok_balance', 'initial_bagi_hasil_balance']);
        });
    }
};
