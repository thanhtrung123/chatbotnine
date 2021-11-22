<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexTblResponseInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_response_info', function(Blueprint $table)
        {
            $table->index('action_datetime');
            $table->index('api_id');
            $table->index('user_ip');
            $table->index('status');
            $table->index('chat_id');
            $table->index('talk_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_response_info', function (Blueprint $table)
        {
            $table->dropIndex(['action_datetime']);
            $table->dropIndex(['api_id']);
            $table->dropIndex(['user_ip']);
            $table->dropIndex(['status']);
            $table->dropIndex(['chat_id']);
            $table->dropIndex(['talk_id']);
        });
    }
}
