<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        // localizable
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug'); // slug
            $table->string('user_type'); // model class
            $table->boolean('available')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_types');
    }
};
