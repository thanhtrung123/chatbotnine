<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSnsUidMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_sns_uid_map', function (Blueprint $table) {
            $table->string('sns_uid', 40);
            $table->integer('channel')->default(config('const.bot.channel.web.id'));
            $table->string('chat_id', 16);
            $table->string('enquete_key', 16)->nullable();
            $table->timestamps();
            $table->primary('sns_uid');
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
        Schema::dropIfExists('tbl_sns_uid_map');
    }
}
