<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToScenarioLearningRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_scenario_learning_relation', function (Blueprint $table) {
            //$table->integer('node_id')->default(0)->nullable()->after('api_id')->comment('Save copy QA Learning');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_scenario_learning_relation', function (Blueprint $table) {
            $table->dropColumn('node_id');
        });
    }
}