<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTitipansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('titipans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debtor_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Index untuk performa query
            $table->index('debtor_id');
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('titipans');
    }
}
