<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_management', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('order_id');
            $table->string('shippping_address');
            $table->integer('cancellation_reason')->nullable();
            $table->integer('cancellation_comment')->nullable();
            $table->string('payment_method')->default(0)->comment('1:online|2:cod');
            $table->string('order_status')->default(0)->comment('1:pending|2:ongoing|3:completed|4:cancelled');
            $table->string('payment_status')->default(0)->comment('1:completed|2:failed');
            $table->integer('total_amount');
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
        Schema::dropIfExists('order_management');
    }
}
