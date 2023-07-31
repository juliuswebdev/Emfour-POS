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
        Schema::create('sale_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_number')->nullable();
            $table->string('cashier_id')->nullable();
            $table->string('register_id')->nullable();
            $table->string('till_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('event_sequence')->nullable();
            $table->timestamp('s_datetime')->nullable();
            $table->timestamp('e_datetime')->nullable();
            $table->date('b_date')->nullable();
            $table->timestamp('r_datetime')->nullable();
            $table->string('route_flag')->nullable();
            $table->string('mode_flag')->nullable();
            $table->string('offline_flag')->nullable();
            $table->string('suspended_flag')->nullable();
            $table->double('total_gross_amount', 5, 2)->default(0);
            $table->double('total_net_amount', 5, 2)->default(0);
            $table->double('total_tax_sales_amount', 5, 2)->default(0);
            $table->double('total_tax_exempt_amount', 5, 2)->default(0);
            $table->double('total_tax_net_amount', 5, 2)->default(0);
            $table->string('total_grand_amount')->nullable();
            $table->double('sum_amount_collected', 5, 2)->default(0);
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
        Schema::dropIfExists('sale_headers');
    }
};
