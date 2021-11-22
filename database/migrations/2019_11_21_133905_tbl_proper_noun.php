<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblProperNoun extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_proper_noun', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('proper_noun_id');
            $table->string('word', 255);
            $table->index(['proper_noun_id'], 'index1');

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
        Schema::dropIfExists('tbl_proper_noun');
    }
}
