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
        Schema::create('count_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('count_header_id');
            $table->integer('product_id');
            $table->string('sku');
            $table->string('upc')->nullable();
            $table->integer('frozen_quantity')->default(0);
            $table->integer('count_quantity')->default(0);
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
        Schema::dropIfExists('count_detail');
    }
};
