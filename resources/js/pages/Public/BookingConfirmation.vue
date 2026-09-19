<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CheckCircle } from '@lucide/vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';

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
    <SeoHead
        :title="`Booking Confirmed · ${booking.reference} | TopGrade London FC`"
        description="TopGrade London FC training session booking confirmation."
        :path="`/bookings/confirmation/${booking.reference}`"
        :noindex="true"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text relative py-12 sm:py-20 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
        <!-- Pitch Grid overlay effect -->
        <div class="w-full max-w-2xl rounded-xs border border-tg-border bg-tg-bg-deep/90 p-6 sm:p-12 shadow-2xl relative z-10 backdrop-blur-none">
            <!-- Success Icon -->
            <div class="flex justify-center">
                <div class="rounded-full bg-tg-accent/15 border border-tg-accent/30 p-3">
                    <CheckCircle class="h-12 w-12 text-tg-accent" />
                </div>
            </div>

            <!-- Confirmation Message -->
            <h1
                class="mt-6 text-center text-3xl sm:text-4xl font-normal uppercase tracking-tight text-tg-text-strong"
                style="font-family: var(--tg-display);"
            >
                Booking Confirmed!
            </h1>
            <p class="mt-2 text-center text-tg-text-muted text-sm sm:text-base max-w-md mx-auto">
                Your session has been successfully booked. A confirmation email has been dispatched to
                <span class="font-semibold text-tg-text-strong">{{ booking.participant_email }}</span>
            </p>

            <!-- Booking Reference -->
            <div class="mt-8 rounded-xs bg-tg-bg border border-tg-border p-5 text-center">
                <p class="text-xs uppercase tracking-widest text-tg-text-muted font-semibold">Booking Reference</p>
                <p class="mt-1 font-mono text-2xl sm:text-3xl font-bold text-tg-accent tracking-wider">{{ booking.reference }}</p>
                <p class="mt-1 text-xs text-tg-text-muted">Save this reference for check-in</p>
            </div>

            <!-- Booking Details -->
            <div class="mt-8 space-y-5">
                <!-- Activity -->
                <div class="border-t border-tg-border pt-5">
                    <p class="text-xs uppercase tracking-widest text-tg-accent font-semibold">Activity Session</p>
                    <p
                        class="mt-1 text-xl sm:text-2xl font-normal uppercase text-tg-text-strong"
                        style="font-family: var(--tg-display);"
                    >
                        {{ booking.bookable_item.name }}
                    </p>
                    <p class="mt-1 text-sm text-tg-text-muted">
                        {{ booking.bookable_item.duration_minutes }} minutes • {{ booking.bookable_item.location }}
                    </p>
                </div>

                <!-- Scheduled -->
                <div class="border-t border-tg-border pt-5">
                    <p class="text-xs uppercase tracking-widest text-tg-accent font-semibold">Scheduled Date & Time</p>
                    <p class="mt-1 text-base sm:text-lg font-semibold text-tg-text-strong">{{ formatScheduled(booking.scheduled_at) }}</p>
                    <p class="mt-0.5 text-xs text-tg-text-muted">Timezone: {{ booking.timezone }}</p>
                </div>

                <!-- Participant -->
                <div class="border-t border-tg-border pt-5">
                    <p class="text-xs uppercase tracking-widest text-tg-accent font-semibold">Participant Details</p>
                    <p class="mt-1 font-semibold text-tg-text-strong">{{ booking.participant_name }}</p>
                    <p class="mt-0.5 text-sm text-tg-text-muted">{{ booking.participant_email }}</p>
                    <p v-if="booking.participant_phone" class="mt-0.5 text-sm text-tg-text-muted">
                        {{ booking.participant_phone }}
                    </p>
                </div>

                <!-- Price -->
                <div class="border-t border-tg-border pt-5">
                    <p class="text-xs uppercase tracking-widest text-tg-accent font-semibold">Session Fee</p>
                    <p class="mt-1 text-2xl sm:text-3xl font-bold text-tg-text-strong">
                        {{ formatPrice(booking.amount, booking.currency) }}
                    </p>
                </div>

                <!-- Status -->
                <div class="border-t border-tg-border pt-5">
                    <p class="text-xs uppercase tracking-widest text-tg-accent font-semibold">Booking Status</p>
                    <div class="mt-1.5 flex items-center gap-2">
                        <div class="h-2.5 w-2.5 rounded-full bg-tg-accent"></div>
                        <p class="font-semibold text-tg-text-strong text-sm">
                            {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
                        </p>
                    </div>
                    <p class="mt-1 text-xs text-tg-text-muted">
                        Your booking is confirmed. We will send session reminders prior to kick-off.
                    </p>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="mt-8 rounded-xs border border-tg-border bg-tg-bg p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-tg-accent">What Happens Next?</p>
                <ul class="mt-3 space-y-2 text-sm text-tg-text">
                    <li class="flex items-start gap-2">
                        <span class="text-tg-accent font-bold">✓</span>
                        <span>Check your inbox for confirmation and session guidelines</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-tg-accent font-bold">✓</span>
                        <span>You will receive an automated reminder 24 hours prior to training</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-tg-accent font-bold">✓</span>
                        <span>Arrive 15 minutes before kick-off for boots on & coach sign-in</span>
                    </li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <Link
                    href="/bookings"
                    class="tg-btn ghost flex-1 justify-center text-center text-xs py-3"
                >
                    Browse More Sessions
                </Link>
                <Link
                    href="/"
                    class="tg-btn flex-1 justify-center text-center text-xs py-3"
                >
                    Back to Home
                </Link>
            </div>
        </div>
    </div>
</template>
