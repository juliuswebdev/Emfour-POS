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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('tips_amount', 22,4)->default(0)->after('selling_price_group_id');
            $table->string('gratuity_label')->nullable()->after('tips_amount');
            $table->decimal('gratuity_charge_percentage', 22,2)->default(0)->after('gratuity_label');
            $table->decimal('gratuity_charge_amount', 22,4)->default(0)->after('gratuity_charge_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
