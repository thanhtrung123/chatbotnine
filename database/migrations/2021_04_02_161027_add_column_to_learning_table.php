<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToLearningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_learning', function (Blueprint $table) {
            $table->dateTime('update_at')->after('auto_key_phrase_disabled');
            $table->dateTime('synced_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_learning', function (Blueprint $table) {
            $table->dateTime('update_at');
            $table->dateTime('synced_at');
        });
    }
}
