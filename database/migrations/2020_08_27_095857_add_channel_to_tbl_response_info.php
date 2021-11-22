<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChannelToTblResponseInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // Schema::table('tbl_response_info', function (Blueprint $table) {
        //     $table->integer('channel')->default(config('const.bot.channel.web.id'))->after('id');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_response_info', function (Blueprint $table) {
            $table->dropColumn('channel');
        });

    }
}
