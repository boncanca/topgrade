<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle } from '@lucide/vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface Activity {
    id: number;
    name: string;
    duration_minutes: number;
    location: string;
    price: string;
    currency: string;
}

interface Booking {
    id: number;
    reference: string;
    participant_name: string;
    participant_email: string;
    participant_phone: string | null;
    scheduled_at: string;
    timezone: string;
    status: string;
    payment_status: string;
    amount: string | null;
    currency: string;
    bookable_item: Activity;
}

defineProps<{
    booking: Booking;
}>();

defineOptions({
    layout: PublicLayout,
});

function formatScheduled(dateStr: string): string {
    const date = new Date(dateStr);
    return new Intl.DateTimeFormat(undefined, {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

function formatPrice(price: string | null, currency: string): string {
    if (!price || price === '0') {
        return 'Free';
    }
    return new Intl.NumberFormat(undefined, {
        style: 'currency',
        currency: currency || 'USD',
    }).format(parseFloat(price));
}
</script>

<template>
    <Head :title="`Booking Confirmed - ${booking.reference}`" />

    <div class="bg-white">
        <div class="min-h-screen">
        <!-- Confirmation Section -->
        <section class="flex items-center justify-center px-4 py-20 sm:px-6 lg:px-8">
            <div class="w-full max-w-2xl rounded-lg border border-slate-200 bg-white p-8 sm:p-12 shadow-md">
                <!-- Success Icon -->
                <div class="flex justify-center">
                    <CheckCircle class="h-16 w-16 text-green-500" />
                </div>

                <!-- Confirmation Message -->
                <h1 class="mt-6 text-center text-4xl font-bold text-slate-900">Booking Confirmed!</h1>
                <p class="mt-2 text-center text-slate-600">
                    Your booking has been successfully created. A confirmation email has been sent to
                    <span class="font-semibold text-slate-900">{{ booking.participant_email }}</span>
                </p>

                <!-- Booking Reference -->
                <div class="mt-8 rounded-lg bg-slate-50 p-4 text-center">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Booking Reference</p>
                    <p class="mt-2 font-mono text-2xl font-bold text-purple-600">{{ booking.reference }}</p>
                    <p class="mt-2 text-xs text-slate-600">Save this for your records</p>
                </div>

                <!-- Booking Details -->
                <div class="mt-8 space-y-6">
                    <!-- Activity -->
                    <div class="border-t border-slate-200 pt-6">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Activity</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ booking.bookable_item.name }}</p>
                        <p class="mt-2 text-sm text-slate-600">
                            {{ booking.bookable_item.duration_minutes }} minutes • {{ booking.bookable_item.location }}
                        </p>
                    </div>

                    <!-- Scheduled -->
                    <div class="border-t border-slate-200 pt-6">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Scheduled For</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ formatScheduled(booking.scheduled_at) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Timezone: {{ booking.timezone }}</p>
                    </div>

                    <!-- Participant -->
                    <div class="border-t border-slate-200 pt-6">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Participant</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ booking.participant_name }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ booking.participant_email }}</p>
                        <p v-if="booking.participant_phone" class="mt-1 text-sm text-slate-600">
                            {{ booking.participant_phone }}
                        </p>
                    </div>

                    <!-- Price -->
                    <div class="border-t border-slate-200 pt-6">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Price</p>
                        <p class="mt-2 text-3xl font-bold text-purple-600">
                            {{ formatPrice(booking.amount, booking.currency) }}
                        </p>
                    </div>

                    <!-- Status -->
                    <div class="border-t border-slate-200 pt-6">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Booking Status</p>
                        <div class="mt-2 flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                            <p class="font-semibold text-slate-900">{{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}</p>
                        </div>
                        <p class="mt-2 text-sm text-slate-600">
                            Your booking is pending confirmation. We'll notify you once it's confirmed.
                        </p>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="mt-8 rounded-lg border border-purple-200 bg-purple-50 p-4">
                    <p class="font-semibold text-purple-900">What happens next?</p>
                    <ul class="mt-3 space-y-2 text-sm text-purple-800">
                        <li>✓ Check your email for confirmation details</li>
                        <li>✓ You'll receive a reminder 24 hours before the activity</li>
                        <li>✓ Arrive 15 minutes early for check-in</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <Link
                        href="/bookings"
                        class="flex-1 rounded-lg border border-slate-300 bg-white px-6 py-3 text-center font-semibold text-slate-900 transition-colors hover:bg-slate-50"
                    >
                        Browse More Sessions
                    </Link>
                    <Link
                        href="/"
                        class="flex-1 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-3 text-center font-semibold text-white transition-transform hover:scale-105"
                    >
                        Back to Home
                    </Link>
                </div>
            </div>
        </section>
        </div>
    </div>
</template>
