<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();;
            $table->string('phone_number')->nullable();
            $table->string('trx_id')->nullable();
            $table->bigInteger('car_service_id')->nullable();
            $table->bigInteger('car_store_id')->nullable();
            $table->unsignedBigInteger('total_amount')->nullable();
            $table->string('proof')->nullable();
            $table->boolean('is_paid')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_transactions');
    }
};
