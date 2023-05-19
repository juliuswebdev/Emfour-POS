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
        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->decimal('original_amount', 22, 4)->default(0)->after('amount');
            $table->decimal('card_charge_amount', 22, 4)->default(0)->after('original_amount');
            $table->decimal('card_charge_percent', 22, 2)->default(0)->comment('By Percent')->after('card_charge_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_payments', function (Blueprint $table) {
            //
        });
    }
};
