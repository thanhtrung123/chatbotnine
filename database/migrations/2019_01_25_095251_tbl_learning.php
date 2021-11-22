<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblLearning extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_learning', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_id');
            $table->text('question_morph');
            $table->text('question');
            $table->text('answer');
            $table->text('metadata')->nullable();
            $table->integer('category_id')->nullable();
            $table->tinyInteger('auto_key_phrase_disabled')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_learning');
    }
}