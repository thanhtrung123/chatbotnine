<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScenarioKeywordRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_scenario_keyword_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('scenario_id');
            $table->integer('scenario_keyword_id');
            $table->index('scenario_id', 'index1');
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
        Schema::dropIfExists('tbl_scenario_keyword_relation');
    }
}
