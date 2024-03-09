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
        Schema::create('change_user_type_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('user_type_class');
            $table->unsignedTinyInteger('status_id')->default(2);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('change_user_type_requests');
    }
};
