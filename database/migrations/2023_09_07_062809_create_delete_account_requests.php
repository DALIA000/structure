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
        Schema::create('delete_account_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->boolean('status_id')->default(0)->comment('0 unread, 1 read, 2 delete'); // pending
            $table->string('full_name'); 
            $table->string('user_type'); 
            $table->unsignedBigInteger('followings_count')->default(0); 
            $table->unsignedBigInteger('followers_count')->default(0); 
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
        Schema::dropIfExists('delete_account_requests');
    }
};
