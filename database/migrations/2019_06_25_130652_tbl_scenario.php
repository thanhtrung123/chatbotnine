<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblScenario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_scenario', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->integer('category_id')->nullable();
//            $table->integer('api_id')->nullable();
            $table->integer('order')->nullable();
            $table->index('category_id', 'index1');
//            $table->index('api_id', 'index2');
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
        Schema::dropIfExists('tbl_scenario');
    }
}
