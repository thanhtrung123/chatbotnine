<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblResponseInfoTruth extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_response_info_truth', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('info_id');
            $table->string('yes_word', 255)->nullable();
            $table->string('no_word', 255)->nullable();
            $table->index('info_id', 'index1');
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
        Schema::dropIfExists('tbl_response_info_truth');
    }
}