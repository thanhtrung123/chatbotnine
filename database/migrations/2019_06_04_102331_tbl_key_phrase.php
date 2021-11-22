<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblKeyPhrase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_key_phrase', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('key_phrase_id');
            $table->string('original_word', 255);
            $table->string('word', 255);
            $table->string('replace_word', 255)->nullable();
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('disabled')->default(0);
            $table->integer('priority')->default(0);
            $table->index('word', 'index1');
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
        Schema::dropIfExists('tbl_key_phrase');
    }
}
