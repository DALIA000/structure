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
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id')->nullable();
      $table->string('user_type_class');
      $table->string('username')->unique();
      $table->string('email')->nullable()->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('phone')->nullable();
      $table->timestamp('phone_verified_at')->nullable();
      $table->string('password')->nullable();
      $table->string('bio')->nullable();
      $table->string('birthday')->nullable();
      $table->unsignedBigInteger('city_id')->default(1); // [ref: > cities.id]
      $table->unsignedBigInteger('club_id')->nullable(); // [ref: > clubs.id]
      $table->text('keywords')->nullable()->comment('for search');
      $table->rememberToken();
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
    Schema::dropIfExists('users');
  }
};
