<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Schedule represents a specific session/occurrence of an Activity.
     * Example: Mini Kickers has multiple Saturday 09:00-09:45 schedules.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bookable_item_id')
                ->constrained('bookable_items')
                ->cascadeOnDelete();

            // When this schedule occurs
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');

            // Capacity for this specific session (can override activity capacity)
            $table->integer('capacity')->nullable();

            // Location for this specific session (can override activity location)
            $table->string('location')->nullable();

            // Session status: active, cancelled, full, etc.
            $table->string('status')->default('active');

            // Booking count (denormalized for performance)
            $table->integer('bookings_count')->default(0);

            // Recurrence info (for future recurring schedules)
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly, etc.

            $table->timestamps();

            // Indexes for common queries
            $table->index('bookable_item_id');
            $table->index('starts_at');
            $table->index('status');
            $table->index(['bookable_item_id', 'starts_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
