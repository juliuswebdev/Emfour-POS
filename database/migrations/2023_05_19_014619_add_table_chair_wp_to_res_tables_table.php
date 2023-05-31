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
        Schema::table('res_tables', function (Blueprint $table) {
            $table->string('wpc_table')->after('description')->nullable();
            $table->string('wpc_chairs')->after('wpc_table')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('res_tables', function (Blueprint $table) {
            //
        });
    }
};
