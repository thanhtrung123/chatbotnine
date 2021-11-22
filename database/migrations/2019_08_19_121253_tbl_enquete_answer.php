<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblEnqueteAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_enquete_answer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('form_id', 255);
            $table->integer('post_id');
            $table->integer('question_code');
            $table->longText('answer')->nullable();
            $table->string('chat_id', 16);
            $table->timestamp('posted_at');
            $table->index(['form_id'], 'index1');
            $table->index(['chat_id'], 'index2');
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
        Schema::dropIfExists('tbl_enquete_answer');
    }
}
