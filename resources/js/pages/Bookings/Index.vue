<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import {
    index as bookingsIndex,
    show as bookingsShow,
} from '@/routes/bookings';

interface Activity {
    id: number;
    name: string;
}

interface Schedule {
    id: number;
    starts_at: string;
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
    scheduled_at: string;
    status: 'pending' | 'confirmed' | 'completed' | 'cancelled';
    payment_status: 'unpaid' | 'pending' | 'paid' | 'refunded';
    amount: number | null;
    currency: string;
    created_at: string;
    bookable_item: Activity;
    schedule: Schedule;
    contact: Contact | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedBookings {
    data: Booking[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

const props = defineProps<{
    bookings: PaginatedBookings;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Bookings',
                href: bookingsIndex(),
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

function formatScheduled(dateStr: string): string {
    const date = new Date(dateStr);

    return new Intl.DateTimeFormat(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
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

function paginationLabel(label: string): string {
    return label.replace('&laquo;', 'Previous').replace('&raquo;', 'Next');
}
</script>

<template>
    <Head title="Bookings" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Bookings"
            description="View and manage all bookings, confirmations, and status changes."
        />

        <div
            v-if="props.bookings.data.length > 0"
            class="border-border bg-background overflow-hidden rounded-lg border"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px] table-auto">
                    <thead class="border-border bg-muted/40 border-b">
                        <tr>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Reference
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Participant
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Activity
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Scheduled
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Status
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Payment
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-right text-xs font-medium uppercase tracking-wide"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-border divide-y">
                        <tr
                            v-for="booking in props.bookings.data"
                            :key="booking.id"
                            class="hover:bg-muted/40 transition-colors"
                        >
                            <td class="px-4 py-4">
                                <Link
                                    :href="bookingsShow(booking.id)"
                                    class="text-foreground text-sm font-medium underline-offset-4 hover:underline"
                                >
                                    {{ booking.reference }}
                                </Link>
                            </td>
                            <td class="text-muted-foreground px-4 py-4 text-sm">
                                {{ booking.participant_name }}
                            </td>
                            <td class="text-muted-foreground px-4 py-4 text-sm">
                                {{ booking.bookable_item.name }}
                            </td>
                            <td
                                class="text-muted-foreground whitespace-nowrap px-4 py-4 text-sm"
                            >
                                {{ formatScheduled(booking.scheduled_at) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <span
                                    :class="[
                                        statusColors[booking.status],
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                    ]"
                                >
                                    {{
                                        booking.status.charAt(0).toUpperCase() +
                                        booking.status.slice(1)
                                    }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="flex flex-col gap-1">
                                    <span
                                        :class="[
                                            paymentColors[
                                                booking.payment_status
                                            ],
                                            'inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        ]"
                                    >
                                        {{
                                            booking.payment_status
                                                .charAt(0)
                                                .toUpperCase() +
                                            booking.payment_status.slice(1)
                                        }}
                                    </span>
                                    <span class="text-muted-foreground text-xs">
                                        {{
                                            formatPrice(
                                                booking.amount,
                                                booking.currency,
                                            )
                                        }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="bookingsShow(booking.id)">
                                            View
                                        </Link>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="border-border text-muted-foreground flex flex-col gap-4 border-t px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between"
            >
                <p>
                    Showing {{ props.bookings.from }} to
                    {{ props.bookings.to }} of {{ props.bookings.total }}
                    entries
                </p>

                <div class="flex flex-wrap gap-1">
                    <template
                        v-for="link in props.bookings.links"
                        :key="`${link.label}-${link.url}`"
                    >
                        <Button
                            v-if="link.url"
                            as-child
                            size="sm"
                            :variant="link.active ? 'default' : 'outline'"
                        >
                            <Link :href="link.url" preserve-scroll>
                                {{ paginationLabel(link.label) }}
                            </Link>
                        </Button>
                        <Button v-else size="sm" variant="outline" disabled>
                            {{ paginationLabel(link.label) }}
                        </Button>
                    </template>
                </div>
            </div>
        </div>

        <EmptyState
            v-else
            title="No bookings yet"
            description="Bookings will appear here as customers make reservations."
        />
    </div>
</template>
