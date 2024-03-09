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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoicable_id');
            $table->string('invoicable_type');
            $table->unsignedDecimal('cost');
            $table->unsignedBigInteger('currency_id')->default(2); // sar
            $table->unsignedDecimal('profit_margin')->default(0)->comment('percentage');
            $table->unsignedBigInteger('status_id')->default(2);
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
        Schema::dropIfExists('invoices');
    }
};
