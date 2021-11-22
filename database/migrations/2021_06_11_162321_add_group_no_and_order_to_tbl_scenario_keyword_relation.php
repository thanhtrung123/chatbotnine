<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupNoAndOrderToTblScenarioKeywordRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('tbl_scenario_keyword_relation', function (Blueprint $table) {
            $table->integer('group_no')->after('scenario_keyword_id')->comment('Scenario keyword group number');
            $table->integer('order')->after('group_no')->comment('Scenario keyword order');
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
        Schema::table('tbl_scenario_keyword_relation', function (Blueprint $table) {
            $table->dropColumn('group_no');
            $table->dropColumn('order');
        });
    }
}
