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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bookable_item_id')->constrained('bookable_items')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete();

            $table->string('reference')->unique();
            $table->string('status')->default('pending');
            $table->timestamp('scheduled_at');
            $table->string('timezone')->default('UTC');

            $table->string('participant_name');
            $table->string('participant_email');
            $table->string('participant_phone')->nullable();

            $table->text('notes')->nullable();

            $table->decimal('amount', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('payment_status')->default('unpaid');

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index('reference');
            $table->index('status');
            $table->index('scheduled_at');
            $table->index('bookable_item_id');
            $table->index('contact_id');
            $table->index('schedule_id');
            $table->index('participant_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
