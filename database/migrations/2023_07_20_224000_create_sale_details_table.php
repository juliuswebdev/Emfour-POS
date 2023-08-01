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
        Schema::create('sale_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_number')->nullable();
            $table->string('cashier_id')->nullable();
            $table->string('register_id')->nullable();
            $table->string('till_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->date('b_date')->nullable();
            $table->date('r_date')->nullable();
            $table->integer('link_sequence')->nullable();
            $table->string('upc_format')->nullable();
            $table->string('upc_number')->nullable();
            $table->string('upd_modifier')->nullable();
            $table->string('upd_modifier_value')->nullable();
            $table->text('item_description')->nullable();
            $table->string('dtl_method_name')->nullable();
            $table->double('actual_sell_price', 5, 2)->default(0);
            $table->string('merch_code')->nullable();
            $table->string('sell_unit')->nullable();
            $table->string('promo_id')->nullable();
            $table->string('promo_reason')->nullable();
            $table->double('promo_amount', 5, 2)->default(0);
            $table->double('reg_sell_price', 5, 2)->default(0);
            $table->integer('sell_qty')->nullable();
            $table->double('sell_amount', 5, 2)->default(0);
            $table->string('item_tax')->nullable();
            $table->string('restriction_flag')->nullable();
            $table->string('restriction_type')->nullable();
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
        Schema::dropIfExists('sale_details');
    }
};