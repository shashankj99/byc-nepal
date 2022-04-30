<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBinIdFieldToDriverPickupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_pickups', function (Blueprint $table) {
            $table->dropColumn("no_of_bins");
            $table->foreignId("bin_id")
                ->constrained("bins")
                ->onDelete("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_pickups', function (Blueprint $table) {
            $table->dropColumn("bin_id");
        });
    }
}
