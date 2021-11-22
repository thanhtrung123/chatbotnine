<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblResponseInfo extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_response_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('chat_id', 16)->nullable();
            $table->string('talk_id', 16)->nullable();
            $table->string('user_ip', 255)->default('');
            $table->dateTime('action_datetime');
            $table->string('status', 255)->nullable();
            $table->text('user_input')->nullable();
            $table->text('user_input_morph')->nullable();
            $table->unsignedInteger('api_id')->nullable();
            $table->text('api_answer')->nullable();
            $table->double('api_score', 5, 2)->default(0.00);
            $table->text('api_question')->nullable();
//            $table->string('selection_symbol', 16)->nullable();
            $table->tinyInteger('is_hear_back')->default(0);
            $table->tinyInteger('is_select')->default(0);
            //index
            $table->index(['chat_id'], 'index1');
            $table->index(['talk_id'], 'index2');
            $table->index(['chat_id', 'talk_id'], 'index3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_response_info');
    }
}