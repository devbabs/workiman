<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyToFreelancerOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('freelancer_offers', function (Blueprint $table) {
            $table->string('currency')->after('price')->default('dollar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('freelancer_offers', function (Blueprint $table) {
            //
        });
    }
}