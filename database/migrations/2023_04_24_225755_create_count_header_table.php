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
        Schema::create('count_header', function (Blueprint $table) {

            $table->id();

            $table->string('count_reference');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');

            $table->integer('business_location_id')->unsigned();
            $table->foreign('business_location_id')->references('id')->on('business_locations')->onDelete('cascade');

            $table->longText('description');
            $table->string('count_type');
            $table->integer('count_sku_with_zero_stock_onhand')->default(0);
            $table->integer('frozen_count')->default(0);
            $table->integer('status')->default(0);

            $table->longText('categories')->nullable();
            $table->longText('sub_categories')->nullable();
            $table->longText('brands')->nullable();

            $table->string('final_file')->nullable();
            $table->string('attach_document')->nullable();
            $table->longText('count_note')->nullable();
            $table->longText('post_note')->nullable();
           
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->integer('user_froze_count');
            $table->integer('user_posted_count');
       
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
        Schema::dropIfExists('count_header');
    }
};
