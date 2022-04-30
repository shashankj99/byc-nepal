<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")
                ->constrained("users")
                ->onDelete("CASCADE");
            $table->foreignId("subscription_id")
                ->constrained("subscriptions")
                ->onDelete("CASCADE");
            $table->string("charity", 50)
                ->nullable();
            $table->string("card_type", 50);
            $table->unsignedDouble("amount", 5, 2);
            $table->enum("order_status", ["pending", "accepted", "rejected"])
                ->default("pending");
            $table->enum("payment_status", ["complete", "incomplete"]);
            $table->enum("bin_type", ["drum-bin", "wheelie-bin"]);
            $table->enum("payment_type", ["full", "installment"]);
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
        Schema::dropIfExists('orders');
    }
}
