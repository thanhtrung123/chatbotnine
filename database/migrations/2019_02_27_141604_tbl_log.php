<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblLog extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id', 255)->default('');
            $table->string('user_name', 255)->nullable();
            $table->string('user_role', 255)->nullable();
//            $table->dateTime('action_datetime');
            $table->string('session_id', 255)->nullable();
            $table->integer('processing');
            $table->timestamp('action_datetime')->default(DB::raw('CURRENT_TIMESTAMP'));
            //index
            $table->index(['user_id', 'action_datetime', 'session_id'], 'index1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_log');
    }
}