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
        Schema::create('sale_fuel_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_number')->nullable();
            $table->string('cashier_id')->nullable();
            $table->string('register_id')->nullable();
            $table->string('till_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->date('b_date')->nullable();
            $table->date('r_date')->nullable();
            $table->integer('tax_sequence')->nullable();
            $table->string('fuel_grade_id')->nullable();
            $table->string('fuel_position_id')->nullable();
            $table->string('price_tier_code')->nullable();
            $table->string('time_tier_code')->nullable();
            $table->string('srv_level_code')->nullable();
            $table->text('description')->nullable();
            $table->string('entry_method_name')->nullable();
            $table->double('actual_sell_price', 5, 2)->default(0);
            $table->string('merchandise_code')->nullable();
            $table->double('reg_sell_price', 5, 2)->default(0);
            $table->integer('sell_qty')->nullable();
            $table->double('sell_amount', 5, 2)->default(0);
            $table->string('tax_level_id')->nullable();
            $table->integer('process_flag')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_fuel_details');
    }
};
