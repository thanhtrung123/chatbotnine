<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblResponseInfoUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_response_info_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('chat_id', 16);
            $table->string('referrer', 255);
            $table->string('remote_ip', 255);
            $table->string('useragent', 255);
            $table->integer('os_id');
            $table->string('os_version', 16)->nullable();
            $table->integer('browser_id');
            $table->string('browser_version', 16)->nullable();
            $table->integer('status');
            $table->timestamp('created_at')->nullable();
            $table->index(['chat_id'], 'index1');

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
        Schema::dropIfExists('tbl_response_info_user');
    }
}
