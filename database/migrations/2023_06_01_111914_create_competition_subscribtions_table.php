<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competition_subscribtions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->references('id')->on('competitions');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->boolean('status')->default(0)->comment('is_winner');
            $table->softDeletes();
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
        Schema::dropIfExists('competition_subscribtions');
    }
};
