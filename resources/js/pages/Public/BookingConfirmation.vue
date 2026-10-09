<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { CheckCircle, Clock, AlertCircle, XCircle, Copy, Check, Building2 } from '@lucide/vue';
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

interface Payment {
    id: number;
    gateway: string;
    status: string;
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
    latest_payment?: Payment | null;
}

interface BankDetails {
    account_name: string;
    bank_name: string;
    sort_code: string;
    account_number: string;
    payment_instructions?: string;
}

const props = defineProps<{
    booking: Booking;
    bank?: BankDetails;
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

    // Default paid flow while awaiting payment
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
            if (props.booking.latest_payment?.gateway === 'stripe') {
                return {
                    title: 'Payment Processing',
                    description: `We've received your booking and are waiting for payment confirmation from Stripe. Once confirmed, your booking will activate and an email will be sent to ${props.booking.participant_email}.`,
                    statusLabel: 'Awaiting Gateway Confirmation',
                    badgeClass: 'bg-amber-500/15 border-amber-500/30 text-amber-400',
                    icon: Clock,
                    iconColor: 'text-amber-400',
                };
            }
            return {
                title: 'Booking Reserved · Awaiting Bank Transfer',
                description: `Your place has been reserved. Please transfer your session fee to the club bank account below using your Booking Reference as payment reference.`,
                statusLabel: 'Awaiting Bank Transfer',
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

const copiedField = ref<string | null>(null);

function copyToClipboard(text: string, field: string): void {
    if (navigator?.clipboard?.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            copiedField.value = field;
            setTimeout(() => {
                if (copiedField.value === field) {
                    copiedField.value = null;
                }
            }, 2500);
        }).catch(() => {});
    }
}

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
                <div class="mt-1 flex items-center justify-center gap-3">
                    <p class="font-mono text-2xl sm:text-3xl font-bold text-tg-accent tracking-wider">{{ booking.reference }}</p>
                    <button
                        type="button"
                        @click="copyToClipboard(booking.reference, 'booking_ref_main')"
                        class="inline-flex items-center gap-1 text-xs text-tg-accent hover:text-tg-accent-hover px-2.5 py-1 rounded border border-tg-border bg-tg-bg-deep cursor-pointer transition-colors"
                        title="Copy Booking Reference"
                    >
                        <component :is="copiedField === 'booking_ref_main' ? Check : Copy" class="h-3.5 w-3.5" />
                        <span>{{ copiedField === 'booking_ref_main' ? 'Copied' : 'Copy' }}</span>
                    </button>
                </div>
                <p class="mt-1 text-xs text-tg-text-muted">Save this reference for all club communications</p>
            </div>

            <!-- Bank Transfer Details Card (Visible for manual bank transfer bookings awaiting payment) -->
            <div v-if="currentState === 'awaiting_payment' && booking.latest_payment?.gateway !== 'stripe'" class="mt-8 rounded-xs border-2 border-amber-500/40 bg-amber-500/5 p-5 sm:p-6 relative overflow-hidden">
                <div class="flex items-center gap-2 mb-3">
                    <Building2 class="h-5 w-5 text-tg-accent" />
                    <h3 class="text-base sm:text-lg font-bold uppercase tracking-wider text-tg-text-strong" style="font-family: var(--tg-display);">
                        Club Bank Transfer Details
                    </h3>
                </div>

                <p class="text-xs sm:text-sm text-tg-text-muted mb-4">
                    Please transfer the fee of <strong class="text-tg-accent font-mono">{{ formatPrice(booking.amount, booking.currency) }}</strong> using your online banking / banking app:
                </p>

                <div class="space-y-3 bg-tg-bg border border-tg-border rounded-xs p-4">
                    <!-- Bank -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-1.5 border-b border-tg-border/50 text-sm">
                        <span class="text-xs uppercase tracking-wider text-tg-text-muted font-semibold">Bank</span>
                        <span class="font-semibold text-tg-text-strong">{{ bank?.bank_name ?? "LLOYD'S BANK" }}</span>
                    </div>

                    <!-- Account Name -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-1.5 border-b border-tg-border/50 text-sm">
                        <span class="text-xs uppercase tracking-wider text-tg-text-muted font-semibold">Account Name</span>
                        <span class="font-semibold text-tg-text-strong font-mono">{{ bank?.account_name ?? 'TOPGRADE LONDON FC' }}</span>
                    </div>

                    <!-- Sort Code -->
                    <div class="flex items-center justify-between py-1.5 border-b border-tg-border/50 text-sm">
                        <span class="text-xs uppercase tracking-wider text-tg-text-muted font-semibold">Sort Code</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-tg-text-strong text-base">{{ bank?.sort_code ?? '30-99-50' }}</span>
                            <button
                                type="button"
                                @click="copyToClipboard(bank?.sort_code ?? '30-99-50', 'sort_code')"
                                class="inline-flex items-center gap-1 text-[11px] text-tg-accent hover:text-tg-accent-hover px-2 py-0.5 rounded border border-tg-border bg-tg-bg-deep cursor-pointer transition-colors"
                            >
                                <component :is="copiedField === 'sort_code' ? Check : Copy" class="h-3 w-3" />
                                <span>{{ copiedField === 'sort_code' ? 'Copied' : 'Copy' }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Account Number -->
                    <div class="flex items-center justify-between py-1.5 border-b border-tg-border/50 text-sm">
                        <span class="text-xs uppercase tracking-wider text-tg-text-muted font-semibold">Account Number</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-tg-text-strong text-base">{{ bank?.account_number ?? '20184968' }}</span>
                            <button
                                type="button"
                                @click="copyToClipboard(bank?.account_number ?? '20184968', 'account_number')"
                                class="inline-flex items-center gap-1 text-[11px] text-tg-accent hover:text-tg-accent-hover px-2 py-0.5 rounded border border-tg-border bg-tg-bg-deep cursor-pointer transition-colors"
                            >
                                <component :is="copiedField === 'account_number' ? Check : Copy" class="h-3 w-3" />
                                <span>{{ copiedField === 'account_number' ? 'Copied' : 'Copy' }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Payment Reference -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 py-2.5 bg-tg-accent/10 -mx-4 -mb-4 px-4 rounded-b-xs border-t border-tg-accent/20">
                        <div>
                            <span class="text-[11px] uppercase tracking-wider font-bold text-tg-accent block">Payment Reference</span>
                            <span class="text-xs text-tg-text-muted">Must be entered as the transfer note</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-extrabold text-tg-accent text-lg sm:text-xl tracking-wider">{{ booking.reference }}</span>
                            <button
                                type="button"
                                @click="copyToClipboard(booking.reference, 'ref_btn')"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-black bg-tg-accent hover:bg-tg-accent-hover px-3 py-1.5 rounded-xs cursor-pointer transition-colors shadow-sm"
                            >
                                <component :is="copiedField === 'ref_btn' ? Check : Copy" class="h-3.5 w-3.5" />
                                <span>{{ copiedField === 'ref_btn' ? 'Copied!' : 'Copy Reference' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex items-start gap-2 text-xs text-amber-300/90 bg-amber-500/10 p-3 rounded-xs border border-amber-500/20">
                    <span class="font-bold text-amber-400">Notice:</span>
                    <span>{{ bank?.payment_instructions ?? 'Please use your Booking Reference as the payment reference when making the transfer. Once your payment arrives in our account, our club administrators will confirm your booking.' }}</span>
                </div>
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
