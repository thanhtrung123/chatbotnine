<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblResponseTotal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_response_aggregate', function (Blueprint $table) {
            $table->increments('id');
            $table->date('aggregate_date');
            $table->integer('aggregate_base');
            $table->integer('aggregate_type');
            $table->integer('group_id')->nullable();
            $table->text('group_string')->nullable();
            $table->integer('total_value');
            //index
            $table->index(['aggregate_date', 'aggregate_type'], 'index1');
            $table->index(['aggregate_date', 'aggregate_base', 'aggregate_type'], 'index2');
            //
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
        Schema::dropIfExists('tbl_response_aggregate');
    }
}
