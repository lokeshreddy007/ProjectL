<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidderLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bidder_logins', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('ip_address')->nullable();
            $table->string('state')->nullable();
            $table->string('run')->nullable();
            $table->string('lot')->nullable();
            $table->string('item')->nullable();
            $table->string('bidder')->nullable();
            $table->string('user')->nullable();
            $table->float('amount')->nullable();
            $table->string('page_url')->nullable();
            $table->string("useragent")->nullable();
            $table->string("session_number")->nullable();
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
        Schema::dropIfExists('bidder_logins');
    }
}
