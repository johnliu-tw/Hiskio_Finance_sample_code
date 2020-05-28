<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable();
            $table->string('identifier')->nullable();
            $table->boolean('is_company')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_address')->nullable();
            $table->string('email')->nullable();
            $table->integer('donate')->nullable();
            $table->string('donate_code')->nullable();
            $table->integer('carrier_type')->nullable();
            $table->string('carrier_code')->nullable();
            $table->integer('price')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
