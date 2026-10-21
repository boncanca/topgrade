<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    CheckCircle,
    Clock,
    AlertCircle,
    XCircle,
    Copy,
    Check,
    Building2,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

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

type UIState =
    | 'free_confirmed'
    | 'paid_confirmed'
    | 'awaiting_payment'
    | 'failed'
    | 'cancelled';

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
                badgeClass:
                    'bg-emerald-500/15 border-emerald-500/30 text-emerald-400',
                icon: CheckCircle,
                iconColor: 'text-emerald-400',
            };
        case 'paid_confirmed':
            return {
                title: 'Booking Confirmed!',
                description: `Payment received. Your session booking is confirmed and your spot secured. A confirmation email has been dispatched to ${props.booking.participant_email}.`,
                statusLabel: 'Confirmed & Paid',
                badgeClass:
                    'bg-emerald-500/15 border-emerald-500/30 text-emerald-400',
                icon: CheckCircle,
                iconColor: 'text-emerald-400',
            };
        case 'awaiting_payment':
            if (props.booking.latest_payment?.gateway === 'stripe') {
                return {
                    title: 'Payment Processing',
                    description: `We've received your booking and are waiting for payment confirmation from Stripe. Once confirmed, your booking will activate and an email will be sent to ${props.booking.participant_email}.`,
                    statusLabel: 'Awaiting Gateway Confirmation',
                    badgeClass:
                        'bg-amber-500/15 border-amber-500/30 text-amber-400',
                    icon: Clock,
                    iconColor: 'text-amber-400',
                };
            }

            return {
                title: 'Booking Reserved · Awaiting Bank Transfer',
                description: `Your place has been reserved. Please transfer your session fee to the club bank account below using your Booking Reference as payment reference.`,
                statusLabel: 'Awaiting Bank Transfer',
                badgeClass:
                    'bg-amber-500/15 border-amber-500/30 text-amber-400',
                icon: Clock,
                iconColor: 'text-amber-400',
            };
        case 'failed':
            return {
                title: 'Payment Unsuccessful',
                description:
                    'Your payment was not successful and your booking has not been confirmed. Please try booking again or contact the club.',
                statusLabel: 'Payment Failed',
                badgeClass: 'bg-red-500/15 border-red-500/30 text-red-400',
                icon: XCircle,
                iconColor: 'text-red-400',
            };
        case 'cancelled':
        default:
            return {
                title: 'Reservation Expired',
                description:
                    'The reservation session for this booking has expired or was cancelled. Please restart the booking process to reserve your place.',
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
        navigator.clipboard
            .writeText(text)
            .then(() => {
                copiedField.value = field;
                setTimeout(() => {
                    if (copiedField.value === field) {
                        copiedField.value = null;
                    }
                }, 2500);
            })
            .catch(() => {});
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
    const numeric =
        typeof amount === 'number' ? amount : parseFloat(String(amount ?? '0'));

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

    <div
        class="bg-tg-bg text-tg-text relative flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 sm:py-20 lg:px-8"
    >
        <!-- Card Container -->
        <div
            class="rounded-xs border-tg-border bg-tg-bg-deep/90 relative z-10 w-full max-w-2xl border p-6 shadow-2xl sm:p-12"
        >
            <!-- Dynamic State Icon -->
            <div class="flex justify-center">
                <div
                    class="rounded-full border p-3"
                    :class="stateConfig.badgeClass"
                >
                    <component
                        :is="stateConfig.icon"
                        class="h-12 w-12"
                        :class="stateConfig.iconColor"
                    />
                </div>
            </div>

            <!-- Header Message -->
            <h1
                class="text-tg-text-strong mt-6 text-center text-3xl font-normal uppercase tracking-tight sm:text-4xl"
                style="font-family: var(--tg-display)"
            >
                {{ stateConfig.title }}
            </h1>
            <p
                class="text-tg-text-muted mx-auto mt-2 max-w-md text-center text-sm sm:text-base"
            >
                {{ stateConfig.description }}
            </p>

            <!-- Booking Reference -->
            <div
                class="rounded-xs bg-tg-bg border-tg-border mt-8 border p-5 text-center"
            >
                <p
                    class="text-tg-text-muted text-xs font-semibold uppercase tracking-widest"
                >
                    Booking Reference
                </p>
                <div class="mt-1 flex items-center justify-center gap-3">
                    <p
                        class="text-tg-accent font-mono text-2xl font-bold tracking-wider sm:text-3xl"
                    >
                        {{ booking.reference }}
                    </p>
                    <button
                        type="button"
                        @click="
                            copyToClipboard(
                                booking.reference,
                                'booking_ref_main',
                            )
                        "
                        class="text-tg-accent hover:text-tg-accent-hover border-tg-border bg-tg-bg-deep inline-flex cursor-pointer items-center gap-1 rounded border px-2.5 py-1 text-xs transition-colors"
                        title="Copy Booking Reference"
                    >
                        <component
                            :is="
                                copiedField === 'booking_ref_main'
                                    ? Check
                                    : Copy
                            "
                            class="h-3.5 w-3.5"
                        />
                        <span>{{
                            copiedField === 'booking_ref_main'
                                ? 'Copied'
                                : 'Copy'
                        }}</span>
                    </button>
                </div>
                <p class="text-tg-text-muted mt-1 text-xs">
                    Save this reference for all club communications
                </p>
            </div>

            <!-- Bank Transfer Details Card (Visible for manual bank transfer bookings awaiting payment) -->
            <div
                v-if="
                    currentState === 'awaiting_payment' &&
                    booking.latest_payment?.gateway !== 'stripe'
                "
                class="rounded-xs relative mt-8 overflow-hidden border-2 border-amber-500/40 bg-amber-500/5 p-5 sm:p-6"
            >
                <div class="mb-3 flex items-center gap-2">
                    <Building2 class="text-tg-accent h-5 w-5" />
                    <h3
                        class="text-tg-text-strong text-base font-bold uppercase tracking-wider sm:text-lg"
                        style="font-family: var(--tg-display)"
                    >
                        Club Bank Transfer Details
                    </h3>
                </div>

                <p class="text-tg-text-muted mb-4 text-xs sm:text-sm">
                    Please transfer the fee of
                    <strong class="text-tg-accent font-mono">{{
                        formatPrice(booking.amount, booking.currency)
                    }}</strong>
                    using your online banking / banking app:
                </p>

                <div
                    class="bg-tg-bg border-tg-border rounded-xs space-y-3 border p-4"
                >
                    <!-- Bank -->
                    <div
                        class="border-tg-border/50 flex flex-col border-b py-1.5 text-sm sm:flex-row sm:items-center sm:justify-between"
                    >
                        <span
                            class="text-tg-text-muted text-xs font-semibold uppercase tracking-wider"
                            >Bank</span
                        >
                        <span class="text-tg-text-strong font-semibold">{{
                            bank?.bank_name ?? "LLOYD'S BANK"
                        }}</span>
                    </div>

                    <!-- Account Name -->
                    <div
                        class="border-tg-border/50 flex flex-col border-b py-1.5 text-sm sm:flex-row sm:items-center sm:justify-between"
                    >
                        <span
                            class="text-tg-text-muted text-xs font-semibold uppercase tracking-wider"
                            >Account Name</span
                        >
                        <span
                            class="text-tg-text-strong font-mono font-semibold"
                            >{{
                                bank?.account_name ?? 'TOPGRADE LONDON FC'
                            }}</span
                        >
                    </div>

                    <!-- Sort Code -->
                    <div
                        class="border-tg-border/50 flex items-center justify-between border-b py-1.5 text-sm"
                    >
                        <span
                            class="text-tg-text-muted text-xs font-semibold uppercase tracking-wider"
                            >Sort Code</span
                        >
                        <div class="flex items-center gap-2">
                            <span
                                class="text-tg-text-strong font-mono text-base font-bold"
                                >{{ bank?.sort_code ?? '30-99-50' }}</span
                            >
                            <button
                                type="button"
                                @click="
                                    copyToClipboard(
                                        bank?.sort_code ?? '30-99-50',
                                        'sort_code',
                                    )
                                "
                                class="text-tg-accent hover:text-tg-accent-hover border-tg-border bg-tg-bg-deep inline-flex cursor-pointer items-center gap-1 rounded border px-2 py-0.5 text-[11px] transition-colors"
                            >
                                <component
                                    :is="
                                        copiedField === 'sort_code'
                                            ? Check
                                            : Copy
                                    "
                                    class="h-3 w-3"
                                />
                                <span>{{
                                    copiedField === 'sort_code'
                                        ? 'Copied'
                                        : 'Copy'
                                }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Account Number -->
                    <div
                        class="border-tg-border/50 flex items-center justify-between border-b py-1.5 text-sm"
                    >
                        <span
                            class="text-tg-text-muted text-xs font-semibold uppercase tracking-wider"
                            >Account Number</span
                        >
                        <div class="flex items-center gap-2">
                            <span
                                class="text-tg-text-strong font-mono text-base font-bold"
                                >{{ bank?.account_number ?? '20184968' }}</span
                            >
                            <button
                                type="button"
                                @click="
                                    copyToClipboard(
                                        bank?.account_number ?? '20184968',
                                        'account_number',
                                    )
                                "
                                class="text-tg-accent hover:text-tg-accent-hover border-tg-border bg-tg-bg-deep inline-flex cursor-pointer items-center gap-1 rounded border px-2 py-0.5 text-[11px] transition-colors"
                            >
                                <component
                                    :is="
                                        copiedField === 'account_number'
                                            ? Check
                                            : Copy
                                    "
                                    class="h-3 w-3"
                                />
                                <span>{{
                                    copiedField === 'account_number'
                                        ? 'Copied'
                                        : 'Copy'
                                }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Payment Reference -->
                    <div
                        class="bg-tg-accent/10 rounded-b-xs border-tg-accent/20 -mx-4 -mb-4 flex flex-col gap-2 border-t px-4 py-2.5 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <span
                                class="text-tg-accent block text-[11px] font-bold uppercase tracking-wider"
                                >Payment Reference</span
                            >
                            <span class="text-tg-text-muted text-xs"
                                >Must be entered as the transfer note</span
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="text-tg-accent font-mono text-lg font-extrabold tracking-wider sm:text-xl"
                                >{{ booking.reference }}</span
                            >
                            <button
                                type="button"
                                @click="
                                    copyToClipboard(
                                        booking.reference,
                                        'ref_btn',
                                    )
                                "
                                class="bg-tg-accent hover:bg-tg-accent-hover rounded-xs inline-flex cursor-pointer items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-black shadow-sm transition-colors"
                            >
                                <component
                                    :is="
                                        copiedField === 'ref_btn' ? Check : Copy
                                    "
                                    class="h-3.5 w-3.5"
                                />
                                <span>{{
                                    copiedField === 'ref_btn'
                                        ? 'Copied!'
                                        : 'Copy Reference'
                                }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-xs mt-4 flex items-start gap-2 border border-amber-500/20 bg-amber-500/10 p-3 text-xs text-amber-300/90"
                >
                    <span class="font-bold text-amber-400">Notice:</span>
                    <span>{{
                        bank?.payment_instructions ??
                        'Please use your Booking Reference as the payment reference when making the transfer. Once your payment arrives in our account, our club administrators will confirm your booking.'
                    }}</span>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="mt-8 space-y-5">
                <!-- Activity -->
                <div class="border-tg-border border-t pt-5">
                    <p
                        class="text-tg-accent text-xs font-semibold uppercase tracking-widest"
                    >
                        Training Session
                    </p>
                    <p
                        class="text-tg-text-strong mt-1 text-xl font-normal uppercase sm:text-2xl"
                        style="font-family: var(--tg-display)"
                    >
                        {{ booking.bookable_item.name }}
                    </p>
                    <p class="text-tg-text-muted mt-1 text-sm">
                        {{ booking.bookable_item.duration_minutes }} minutes •
                        {{ booking.bookable_item.location }}
                    </p>
                </div>

                <!-- Scheduled -->
                <div class="border-tg-border border-t pt-5">
                    <p
                        class="text-tg-accent text-xs font-semibold uppercase tracking-widest"
                    >
                        Scheduled Date & Time
                    </p>
                    <p
                        class="text-tg-text-strong mt-1 text-base font-semibold sm:text-lg"
                    >
                        {{ formatScheduled(booking.scheduled_at) }}
                    </p>
                    <p
                        v-if="booking.timezone"
                        class="text-tg-text-muted mt-0.5 text-xs"
                    >
                        Timezone: {{ booking.timezone }}
                    </p>
                </div>

                <!-- Participant -->
                <div class="border-tg-border border-t pt-5">
                    <p
                        class="text-tg-accent text-xs font-semibold uppercase tracking-widest"
                    >
                        Participant Details
                    </p>
                    <p class="text-tg-text-strong mt-1 font-semibold">
                        {{ booking.participant_name }}
                    </p>
                    <p class="text-tg-text-muted mt-0.5 text-sm">
                        {{ booking.participant_email }}
                    </p>
                    <p
                        v-if="booking.participant_phone"
                        class="text-tg-text-muted mt-0.5 text-sm"
                    >
                        {{ booking.participant_phone }}
                    </p>
                </div>

                <!-- Price -->
                <div class="border-tg-border border-t pt-5">
                    <p
                        class="text-tg-accent text-xs font-semibold uppercase tracking-widest"
                    >
                        Fee
                    </p>
                    <p
                        class="text-tg-text-strong mt-1 text-2xl font-bold sm:text-3xl"
                    >
                        {{ formatPrice(booking.amount, booking.currency) }}
                    </p>
                </div>

                <!-- Status -->
                <div class="border-tg-border border-t pt-5">
                    <p
                        class="text-tg-accent text-xs font-semibold uppercase tracking-widest"
                    >
                        Status
                    </p>
                    <div class="mt-1.5 flex items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                            :class="stateConfig.badgeClass"
                        >
                            {{ stateConfig.statusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Next Steps for Confirmed Bookings -->
            <div
                v-if="
                    currentState === 'free_confirmed' ||
                    currentState === 'paid_confirmed'
                "
                class="rounded-xs border-tg-border bg-tg-bg mt-8 border p-5"
            >
                <p
                    class="text-tg-accent text-xs font-bold uppercase tracking-wider"
                >
                    What Happens Next?
                </p>
                <ul class="text-tg-text mt-3 space-y-2 text-sm">
                    <li class="flex items-start gap-2">
                        <span class="text-tg-accent font-bold">✓</span>
                        <span
                            >Check your inbox for confirmation and session
                            guidelines</span
                        >
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-tg-accent font-bold">✓</span>
                        <span
                            >Arrive 15 minutes before kick-off for boots on &
                            coach sign-in</span
                        >
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-tg-accent font-bold">✓</span>
                        <span
                            >Mandatory shin guards and football boots required
                            for all sessions</span
                        >
                    </li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <Link
                    href="/training"
                    class="tg-btn ghost flex-1 justify-center py-3 text-center text-xs"
                >
                    View All Training
                </Link>
                <Link
                    href="/"
                    class="tg-btn flex-1 justify-center py-3 text-center text-xs"
                >
                    Back to Home
                </Link>
            </div>
        </div>
    </div>
</template>
