<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblLearningRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_learning_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->integer('api_id');
            $table->integer('relation_api_id');
            $table->integer('order');
            $table->index('api_id', 'index1');
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
        Schema::dropIfExists('tbl_learning_relation');
    }
}
