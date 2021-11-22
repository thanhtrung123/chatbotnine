<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScenarioLearningRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_scenario_learning_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('scenario_id');
            $table->integer('api_id')->nullable();
            $table->integer('order')->nullable();
            $table->index('scenario_id', 'index1');
            $table->index('api_id', 'index2');
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
        Schema::dropIfExists('tbl_scenario_learning_relation');
    }
}
