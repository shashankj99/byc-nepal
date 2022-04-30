<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCharityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charities', function (Blueprint $table) {
            $table->string("account_name", 191);
            $table->string("account_number", 50);
            $table->string("bsb", 15);
            $table->string("bank_name", 191);
            $table->string("branch", 191);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('charities', function (Blueprint $table) {
            $table->dropColumn("account_name", "account_number", "bsb", "bank_name", "branch");
        });
    }
}
