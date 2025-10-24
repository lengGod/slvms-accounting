<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllocationColumnsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('bagi_hasil', 10, 2)->nullable()->after('amount');
            $table->decimal('bagi_pokok', 10, 2)->nullable()->after('bagi_hasil');
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['bagi_hasil', 'bagi_pokok']);
            $table->string('status')->default('belum_lunas');
        });
    }
}
