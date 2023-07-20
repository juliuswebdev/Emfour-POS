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
        Schema::create('sale_taxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_number')->nullable();
            $table->string('cashier_id')->nullable();
            $table->string('register_id')->nullable();
            $table->string('till_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->date('b_date')->nullable();
            $table->date('r_date')->nullable();
            $table->integer('tax_sequence')->nullable();
            $table->string('tax_level_id')->nullable();
            $table->double('taxable_amount', 5, 2)->default(0);
            $table->double('collected_amount', 5, 2)->default(0);
            $table->double('sell_refund_amount', 5, 2)->default(0);
            $table->double('refund_amount', 5, 2)->default(0);
            $table->double('exempt_sell_amount', 5, 2)->default(0);
            $table->double('exempt_sell_reference', 5, 2)->default(0);
            $table->double('forgive_sell_amount', 5, 2)->default(0);
            $table->double('forgive_refund_amount', 5, 2)->default(0);
            $table->double('forgive_amount', 5, 2)->default(0);
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
        Schema::dropIfExists('sale_taxes');
    }
};
