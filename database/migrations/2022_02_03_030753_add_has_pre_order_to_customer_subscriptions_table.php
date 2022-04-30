<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasPreOrderToCustomerSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_subscriptions', function (Blueprint $table) {
            $table->enum("has_pre_order", ["1", "0"])
                ->default("0");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_subscriptions', function (Blueprint $table) {
            $table->dropColumn("has_pre_order");
        });
    }
}
