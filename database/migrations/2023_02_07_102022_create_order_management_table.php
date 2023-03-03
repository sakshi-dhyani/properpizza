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
            $table->integer('user_name');
            $table->string('order_id');
            $table->string('shippping_address');
            $table->string('cancellation_reason')->nullable();
            $table->string('cancellation_comment')->nullable();
            $table->string('payment_method')->default(0)->comment('1:online|2:cod');
            $table->string('order_status')->default(0)->comment('1:pending|2:ongoing|3:completed|4:cancelled|5:accepted');
            $table->string('payment_status')->default(0)->comment('1:completed|2:failed');
            $table->string('transaction_id')->default(0);
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
