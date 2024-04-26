<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_kriteria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kriteria_id');
            $table->integer('kode');
            $table->string('nama');
            $table->double('jumlah_bobot',10)->default(0);
            $table->double('jumlah_eigen',10)->default(0);
            $table->double('rata_eigen',10)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_kriterias');
    }
};
