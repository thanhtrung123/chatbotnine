<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScenarioRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_scenario_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('scenario_id');
            $table->integer('parent_scenario_id')->nullable();
//            $table->integer('child_scenario_id')->nullable();
            $table->index('scenario_id', 'index1');
            $table->index('parent_scenario_id', 'index2');
//            $table->index('child_scenario_id', 'index3');
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
        Schema::dropIfExists('tbl_scenario_relation');
    }
}
