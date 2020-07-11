<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOriginLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('origin_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('method', 16);
            $table->jsonb('params');
            $table->jsonb('data')->nullable();
            $table->text('uri');
            $table->string('remote_addr', 128);
            $table->text('user_agent');
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
        Schema::dropIfExists('origin_logs');
    }
}
