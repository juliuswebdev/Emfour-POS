<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->integer('kitchen_screen_button_undo_timeframe')->nullable()->comments('Used In Kitchen UI(In Sec)')->after('gratuity_settings');
        
            $table->integer('order_screen_button_undo_timeframe')->nullable()->comments('Used In Order UI(In Sec)')->after('kitchen_screen_button_undo_timeframe');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            //
        });
    }
};
