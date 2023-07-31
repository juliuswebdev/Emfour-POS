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
        Schema::create('sale_tenders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_number')->nullable();
            $table->string('cashier_id')->nullable();
            $table->string('register_id')->nullable();
            $table->string('till_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->date('b_date')->nullable();
            $table->date('r_date')->nullable();
            $table->integer('tender_sequence')->nullable();
            $table->string('tender_code')->nullable();
            $table->string('sub_code')->nullable();
            $table->double('tender_amount', 5, 2)->default(0);
            $table->string('change_e_flag')->nullable();
            $table->string('pre_auth_flag')->nullable();
            $table->double('request_amount', 5, 2)->default(0);
            $table->string('auth_response_code')->nullable();
            $table->text('auth_response_description')->nullable();
            $table->string('auth_approve_code')->nullable();
            $table->string('auth_reference_code')->nullable();
            $table->string('auth_provider_id')->nullable();
            $table->timestamp('auth_datetime')->nullable();
            $table->string('host_auth_flag')->nullable();
            $table->text('auth_approve_description')->nullable();
            $table->string('auth_terminal_id')->nullable();
            $table->string('auth_frcol_flag')->nullable();
            $table->string('esignature')->nullable();
            $table->double('auth_charge_amount', 5, 2)->default(0);
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_first_name')->nullable();
            $table->string('account_middle_name')->nullable();
            $table->string('account_last_name')->nullable();
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
        Schema::dropIfExists('sale_tenders');
    }
};
