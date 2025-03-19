<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('reservation_code')->unique();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date')->nullable();
            $table->integer('accompany_number');
            $table->unsignedBigInteger('price_at_booking');
            $table->string('payment_intent_id')->nullable();
            $table->string('payment_method_id')->nullable();
            $table->enum('status', ['pending_payment', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])->default('pending_payment');
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
