<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")
                ->constrained("users")
                ->onDelete("CASCADE");
            $table->enum("bin_type", ["drum-bin", "wheelie-bin"]);
            $table->enum("payment_type", ["full", "installment"])
                ->nullable();
            $table->unsignedDouble("total_amount", 5,2)
                ->nullable()
                ->default(0);
            $table->unsignedDouble("remaining_amount", 5,2)
                ->nullable()
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pre_orders');
    }
}
