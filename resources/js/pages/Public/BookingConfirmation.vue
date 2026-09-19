<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { CheckCircle, Clock, AlertCircle, XCircle } from '@lucide/vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';

interface Activity {
    id?: number;
    name: string;
    duration_minutes: number;
    location: string;
    price?: string;
    currency?: string;
    requires_payment?: boolean;
}

interface Booking {
    id: number;
    reference: string;
    participant_name: string;
    participant_email: string;
    participant_phone?: string | null;
    scheduled_at: string;
    timezone?: string;
    status: string;
    payment_status: string;
    payment_expires_at?: string | null;
    amount: string | number | null;
    currency: string;
    bookable_item: Activity;
}

const props = defineProps<{
    booking: Booking;
}>();

defineOptions({
    layout: PublicLayout,
});

type UIState = 'free_confirmed' | 'paid_confirmed' | 'awaiting_payment' | 'failed' | 'cancelled';

const currentState = computed<UIState>(() => {
    const paymentStatus = props.booking.payment_status;
    const status = props.booking.status;

    if (paymentStatus === 'not_required') {
        return 'free_confirmed';
    }

    if (status === 'confirmed' && paymentStatus === 'paid') {
        return 'paid_confirmed';
    }

    if (paymentStatus === 'failed') {
        return 'failed';
    }

    if (status === 'cancelled' || paymentStatus === 'cancelled') {
        return 'cancelled';
    }

    // Default paid flow while webhook is processing / awaiting confirmation
    return 'awaiting_payment';
});

const stateConfig = computed(() => {
    switch (currentState.value) {
        case 'free_confirmed':
            return {
                title: 'Booking Confirmed!',
                description: `Your free session has been successfully booked. A confirmation email has been dispatched to ${props.booking.participant_email}.`,
                statusLabel: 'Confirmed (Free)',
                badgeClass: 'bg-emerald-500/15 border-emerald-500/30 text-emerald-400',
                icon: CheckCircle,
                iconColor: 'text-emerald-400',
            };
        case 'paid_confirmed':
            return {
                title: 'Booking Confirmed!',
                description: `Payment received. Your session booking is confirmed and your spot secured. A confirmation email has been dispatched to ${props.booking.participant_email}.`,
                statusLabel: 'Confirmed & Paid',
                badgeClass: 'bg-emerald-500/15 border-emerald-500/30 text-emerald-400',
                icon: CheckCircle,
                iconColor: 'text-emerald-400',
            };
        case 'awaiting_payment':
            return {
                title: 'Payment Processing',
                description: `We've received your booking and are waiting for payment confirmation from Stripe. Once confirmed, your booking will activate and an email will be sent to ${props.booking.participant_email}.`,
                statusLabel: 'Awaiting Gateway Confirmation',
                badgeClass: 'bg-amber-500/15 border-amber-500/30 text-amber-400',
                icon: Clock,
                iconColor: 'text-amber-400',
            };
        case 'failed':
            return {
                title: 'Payment Unsuccessful',
                description: 'Your payment was not successful and your booking has not been confirmed. Please try booking again or contact the club.',
                statusLabel: 'Payment Failed',
                badgeClass: 'bg-red-500/15 border-red-500/30 text-red-400',
                icon: XCircle,
                iconColor: 'text-red-400',
            };
        case 'cancelled':
        default:
            return {
                title: 'Reservation Expired',
                description: 'The reservation session for this booking has expired or was cancelled. Please restart the booking process to reserve your place.',
                statusLabel: 'Reservation Expired',
                badgeClass: 'bg-red-500/15 border-red-500/30 text-red-400',
                icon: AlertCircle,
                iconColor: 'text-red-400',
            };
    }
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

function formatPrice(amount: string | number | null, currency: string): string {
    const numeric = typeof amount === 'number' ? amount : parseFloat(String(amount ?? '0'));
    if (!amount || isNaN(numeric) || numeric === 0) {
        return 'Free';
    }
    return new Intl.NumberFormat(undefined, {
        style: 'currency',
        currency: currency || 'GBP',
    }).format(numeric);
}
</script>

<template>
    <SeoHead
        :title="`${stateConfig.title} · ${booking.reference} | TopGrade London FC`"
        description="TopGrade London FC training session booking details."
        :path="`/bookings/confirmation/${booking.reference}`"
        :noindex="true"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text relative py-12 sm:py-20 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
        <!-- Card Container -->
        <div class="w-full max-w-2xl rounded-xs border border-tg-border bg-tg-bg-deep/90 p-6 sm:p-12 shadow-2xl relative z-10">
            <!-- Dynamic State Icon -->
            <div class="flex justify-center">
                <div class="rounded-full p-3 border" :class="stateConfig.badgeClass">
                    <component :is="stateConfig.icon" class="h-12 w-12" :class="stateConfig.iconColor" />
                </div>
            </div>

            <!-- Header Message -->
            <h1
                class="mt-6 text-center text-3xl sm:text-4xl font-normal uppercase tracking-tight text-tg-text-strong"
                style="font-family: var(--tg-display);"
            >
                {{ stateConfig.title }}
            </h1>
            <p class="mt-2 text-center text-tg-text-muted text-sm sm:text-base max-w-md mx-auto">
                {{ stateConfig.description }}
            </p>

            <!-- Booking Reference -->
            <div class="mt-8 rounded-xs bg-tg-bg border border-tg-border p-5 text-center">
                <p class="text-xs uppercase tracking-widest text-tg-text-muted font-semibold">Booking Reference</p>
                <p class="mt-1 font-mono text-2xl sm:text-3xl font-bold text-tg-accent tracking-wider">{{ booking.reference }}</p>
                <p class="mt-1 text-xs text-tg-text-muted">Save this reference for all club communications</p>
            </div>

            <!-- Booking Details -->
            <div class="mt-8 space-y-5">
                <!-- Activity -->
                <div class="border-t border-tg-border pt-5">
                    <p class="text-xs uppercase tracking-widest text-tg-accent font-semibold">Training Session</p>
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
                    <p v-if="booking.timezone" class="mt-0.5 text-xs text-tg-text-muted">Timezone: {{ booking.timezone }}</p>
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
                    <p class="text-xs uppercase tracking-widest text-tg-accent font-semibold">Fee</p>
                    <p class="mt-1 text-2xl sm:text-3xl font-bold text-tg-text-strong">
                        {{ formatPrice(booking.amount, booking.currency) }}
                    </p>
                </div>

                <!-- Status -->
                <div class="border-t border-tg-border pt-5">
                    <p class="text-xs uppercase tracking-widest text-tg-accent font-semibold">Status</p>
                    <div class="mt-1.5 flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="stateConfig.badgeClass">
                            {{ stateConfig.statusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Next Steps for Confirmed Bookings -->
            <div v-if="currentState === 'free_confirmed' || currentState === 'paid_confirmed'" class="mt-8 rounded-xs border border-tg-border bg-tg-bg p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-tg-accent">What Happens Next?</p>
                <ul class="mt-3 space-y-2 text-sm text-tg-text">
                    <li class="flex items-start gap-2">
                        <span class="text-tg-accent font-bold">✓</span>
                        <span>Check your inbox for confirmation and session guidelines</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-tg-accent font-bold">✓</span>
                        <span>Arrive 15 minutes before kick-off for boots on & coach sign-in</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-tg-accent font-bold">✓</span>
                        <span>Mandatory shin guards and football boots required for all sessions</span>
                    </li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <Link
                    href="/training"
                    class="tg-btn ghost flex-1 justify-center text-center text-xs py-3"
                >
                    View All Training
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
