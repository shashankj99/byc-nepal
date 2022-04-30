<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverPickupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_pickups', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")
                ->constrained("users")
                ->onDelete("CASCADE");
            $table->foreignId("user_address_id")
                ->constrained("user_addresses")
                ->onDelete("CASCADE");
            $table->unsignedInteger("no_of_bins");
            $table->timestamp("pickup_date")
                ->nullable();
            $table->enum("status", ["unpicked", "picked"])
                ->default("unpicked");
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
        Schema::dropIfExists('driver_pickups');
    }
}
