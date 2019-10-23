<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransaksiBarangDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_barang_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tr_barang_id');
            $table->unsignedBigInteger('mr_barang_id');
            $table->double('harga')->default(0);
            $table->integer('jumlah')->default(0);
            $table->double('total')->default(0);

            $table->foreign('tr_barang_id')
                ->references('id')
                ->on('tr_barang')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('mr_barang_id')
                ->references('id')
                ->on('mr_barang')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_barang_detail');
    }
}
