<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('business_name');
            $table->decimal('latitude', 13, 10)->nullable();
            $table->decimal('longitude', 13, 10)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('status_id')->default(1); // [ref: > status.id]
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clubs');
    }
};
