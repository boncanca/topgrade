<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import FormSection from '@/components/FormSection.vue';
import PageHeader from '@/components/PageHeader.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import { index as bookingsIndex } from '@/routes/bookings';

interface Activity {
    id: number;
    name: string;
    duration_minutes: number;
    location: string;
}

interface Schedule {
    id: number;
    starts_at: string;
    ends_at: string;
    capacity: number | null;
    location: string | null;
}

interface Contact {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
}

interface Booking {
    id: number;
    reference: string;
    participant_name: string;
    participant_email: string;
    participant_phone: string | null;
    scheduled_at: string;
    timezone: string;
    status: 'pending' | 'confirmed' | 'completed' | 'cancelled';
    payment_status: 'unpaid' | 'pending' | 'paid' | 'refunded';
    amount: number | null;
    currency: string;
    notes: string | null;
    metadata: Record<string, any> | null;
    bookable_item: Activity;
    schedule: Schedule;
    contact: Contact | null;
    created_at: string;
}

const props = defineProps<{
    booking: Booking;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Bookings',
                href: bookingsIndex(),
            },
            {
                title: 'View Booking',
            },
        ],
    },
});

const statusColors = {
    pending: 'bg-blue-100 text-blue-800',
    confirmed: 'bg-green-100 text-green-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const paymentColors = {
    unpaid: 'bg-gray-100 text-gray-800',
    pending: 'bg-amber-100 text-amber-800',
    paid: 'bg-green-100 text-green-800',
    refunded: 'bg-amber-100 text-amber-800',
};

const processing = ref(false);

function formatScheduled(dateStr: string): string {
    const date = new Date(dateStr);
    return new Intl.DateTimeFormat(undefined, {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZoneName: 'short',
    }).format(date);
}

function formatPrice(price: number | null, currency: string): string {
    if (price === null) {
        return 'Free';
    }
    return new Intl.NumberFormat(undefined, {
        style: 'currency',
        currency: currency || 'USD',
    }).format(price);
}

function canTransitionTo(newStatus: string): boolean {
    const transitions: Record<string, string[]> = {
        pending: ['confirmed', 'cancelled'],
        confirmed: ['completed', 'cancelled'],
        completed: [],
        cancelled: [],
    };
    return transitions[props.booking.status]?.includes(newStatus) ?? false;
}

function transitionStatus(newStatus: 'pending' | 'confirmed' | 'completed' | 'cancelled'): void {
    processing.value = true;
    const routeMap = {
        confirmed: `/dashboard/bookings/${props.booking.id}/confirm`,
        completed: `/dashboard/bookings/${props.booking.id}/complete`,
        cancelled: `/dashboard/bookings/${props.booking.id}/cancel`,
    };
    router.post(routeMap[newStatus as keyof typeof routeMap], {}, {
        onFinish: () => { processing.value = false; },
    });
}

function handleCancel(): void {
    router.visit(bookingsIndex());
}
</script>

<template>
    <Head :title="`Booking ${booking.reference}`" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="`Booking ${booking.reference}`"
            :description="booking.participant_name"
        />

        <div class="space-y-8">
            <FormSection
                title="Booking Details"
                description="Core booking information and schedule."
            >
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Reference
                        </label>
                        <p class="text-sm text-foreground">
                            {{ booking.reference }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Status
                        </label>
                        <span
                            :class="[
                                statusColors[booking.status],
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            ]"
                        >
                            {{
                                booking.status.charAt(0).toUpperCase() +
                                booking.status.slice(1)
                            }}
                        </span>
                    </div>

                    <div class="col-span-2">
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Activity
                        </label>
                        <p class="text-sm text-foreground">
                            {{ booking.bookable_item.name }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{
                                booking.bookable_item.duration_minutes
                            }}
                            minutes at {{ booking.bookable_item.location }}
                        </p>
                    </div>

                    <div class="col-span-2">
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Scheduled
                        </label>
                        <p class="text-sm text-foreground">
                            {{ formatScheduled(booking.scheduled_at) }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Timezone: {{ booking.timezone }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <FormSection
                title="Participant"
                description="Contact information for the booking."
            >
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Name
                        </label>
                        <p class="text-sm text-foreground">
                            {{ booking.participant_name }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Email
                        </label>
                        <p class="text-sm text-foreground">
                            {{ booking.participant_email }}
                        </p>
                    </div>

                    <div v-if="booking.participant_phone">
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Phone
                        </label>
                        <p class="text-sm text-foreground">
                            {{ booking.participant_phone }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <FormSection
                title="Payment"
                description="Payment status and amount."
            >
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Status
                        </label>
                        <span
                            :class="[
                                paymentColors[booking.payment_status],
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            ]"
                        >
                            {{
                                booking.payment_status
                                    .charAt(0)
                                    .toUpperCase() +
                                booking.payment_status.slice(1)
                            }}
                        </span>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Amount
                        </label>
                        <p class="text-sm text-foreground">
                            {{ formatPrice(booking.amount, booking.currency) }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <FormSection
                v-if="booking.notes || booking.metadata"
                title="Additional Information"
                description="Notes and metadata."
            >
                <div class="space-y-4">
                    <div v-if="booking.notes">
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Notes
                        </label>
                        <p class="text-sm text-foreground">
                            {{ booking.notes }}
                        </p>
                    </div>

                    <div v-if="booking.metadata && Object.keys(booking.metadata).length > 0">
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Additional Details
                        </label>
                        <dl class="grid gap-2">
                            <div
                                v-for="(value, key) in booking.metadata"
                                :key="key"
                                class="flex justify-between text-sm"
                            >
                                <dt class="text-muted-foreground capitalize">
                                    {{ key.replace(/_/g, ' ') }}:
                                </dt>
                                <dd class="text-foreground">{{ value }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </FormSection>
        </div>

        <PageActions>
            <div class="flex gap-2">
                <Button
                    v-if="canTransitionTo('confirmed')"
                    type="button"
                    @click="transitionStatus('confirmed')"
                    :disabled="processing"
                >
                    {{ processing ? 'Confirming...' : 'Confirm Booking' }}
                </Button>
                <Button
                    v-if="canTransitionTo('completed')"
                    type="button"
                    @click="transitionStatus('completed')"
                    :disabled="processing"
                >
                    {{ processing ? 'Completing...' : 'Mark Complete' }}
                </Button>
                <Button
                    v-if="canTransitionTo('cancelled')"
                    type="button"
                    variant="destructive"
                    @click="transitionStatus('cancelled')"
                    :disabled="processing"
                >
                    {{ processing ? 'Cancelling...' : 'Cancel Booking' }}
                </Button>
            </div>
            <Button
                type="button"
                variant="outline"
                @click="handleCancel"
            >
                Back
            </Button>
        </PageActions>
    </div>
</template>
