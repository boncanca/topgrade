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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('gateway')->default('stripe');
            $table->string('gateway_session_id')->nullable()->index();
            $table->string('gateway_payment_intent_id')->nullable()->index();
            $table->string('status')->default('pending')->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('GBP');
            $table->string('customer_email');
            $table->timestamp('expires_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
