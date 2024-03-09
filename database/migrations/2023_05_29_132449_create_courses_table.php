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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->references('id')->on('videos');
            $table->decimal('individual_price', 8, 2, true);
            $table->foreignId('individual_currency_id')->references('id')->on('currencies');
            $table->decimal('group_discount', 4, 2, true)->nullable();
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('seats_count');
            $table->unsignedBigInteger('status_id')->default(2);
            $table->text('status_note')->nullable();
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
        Schema::dropIfExists('courses');
    }
};
