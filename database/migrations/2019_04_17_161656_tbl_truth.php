<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblTruth extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_truth', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_id');
            $table->integer('category')->nullable();
            $table->integer('key_phrase_id');
            $table->integer('key_phrase_priority')->nullable();
            $table->tinyInteger('auto_key_phrase_priority_disabled')->default(0);
            $table->integer('count')->default(1);
            //index
            $table->index('api_id', 'index1');
            $table->index('key_phrase_id', 'index2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('tbl_truth');
    }
}