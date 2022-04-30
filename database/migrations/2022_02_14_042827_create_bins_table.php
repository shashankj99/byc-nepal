<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bins', function (Blueprint $table) {
            $table->id();
            $table->foreignId("order_id")
                ->nullable()
                ->constrained("orders")
                ->onDelete("CASCADE");
            $table->string("bin_number");
            $table->string("qr_code");
            $table->enum("bin_type", ["wheelie-bin", "drum-bin"]);
            $table->enum("status", ["allocated", "unallocated"]);
            $table->timestamp("decomposition_date")
                ->nullable();
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
        Schema::dropIfExists('bins');
    }
}
