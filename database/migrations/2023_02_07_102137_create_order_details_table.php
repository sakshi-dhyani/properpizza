<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->comment('order id from order_management');
            $table->integer('user_id');
            $table->string('product_id');
            $table->string('small')->default(0);
            $table->string('medium')->default(0);
            $table->string('large')->default(0);
            $table->string('king')->default(0);
            $table->integer('amount')->default(0);
            $table->string('cart_id')->default(0);
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
        Schema::dropIfExists('order_details');
    }
}
