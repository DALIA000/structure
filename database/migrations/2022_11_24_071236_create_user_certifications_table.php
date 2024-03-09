<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_certifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('commercial_certification_document_id')->nullable()->comment('academy');
            $table->timestamp('commercial_certification_verified_at')->nullable();
            $table->string('experience_certification_document_id')->nullable()->comment('trainer');
            $table->timestamp('experience_certification_verified_at')->nullable();
            $table->string('training_certification_document_id')->nullable()->comment('trainer');
            $table->timestamp('training_certification_verified_at')->nullable();
            $table->string('journalism_certification_document_id')->nullable()->comment('journalist');
            $table->timestamp('journalism_certification_verified_at')->nullable();
            $table->string('influencement_certification_document_id')->nullable()->comment('influencer');
            $table->timestamp('influencement_certification_verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_certifications');
    }
};
