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
        Schema::create('promotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotable_id');
            $table->string('promotable_type');
            $table->boolean('status')->default(1);
            $table->dateTime('starts_at')->default(now());
            $table->dateTime('ends_at');
            $table->json('target')->nullable();
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
        Schema::dropIfExists('promotes');
    }
};
