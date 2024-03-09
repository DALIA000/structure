<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('club_players', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->unsignedInteger('player_position_id');
            $table->unsignedBigInteger('club_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('club_players');
    }
};
