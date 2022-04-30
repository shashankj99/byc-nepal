<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionUidToMyobTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('myob_transactions', function (Blueprint $table) {
            $table->string("transaction_uid", 191);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('myob_transactions', function (Blueprint $table) {
            $table->dropColumn("transaction_uid");
        });
    }
}
