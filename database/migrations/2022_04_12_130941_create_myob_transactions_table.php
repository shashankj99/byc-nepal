<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyobTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('myob_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")
                ->nullable()
                ->constrained("users")
                ->onDelete("CASCADE");
            $table->unsignedFloat("amount", 15, 2)
                ->default(0);
            $table->timestamp("payment_date");
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
        Schema::dropIfExists('myob_transactions');
    }
}
