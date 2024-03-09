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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('prize');
            $table->text('price')->nullable();
            $table->unsignedTinyInteger('days');
            $table->unsignedTinyInteger('winners_count')->default(1);
            $table->boolean('type')->default(0)->comment('0 private, 1 public');
            $table->tinyInteger('style')->default(1)->comment('1 description, 2 options');
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
        Schema::dropIfExists('competitions');
    }
};
