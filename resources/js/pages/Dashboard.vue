<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    Calendar,
    CalendarCheck,
    ClipboardList,
    Clock,
    CreditCard,
    DollarSign,
    FileText,
    Inbox,
    Layers,
    Menu,
    Plus,
    Users,
} from '@lucide/vue';
import { dashboard } from '@/routes';
import { create as createBookableItem, index as bookableItemsIndex } from '@/routes/bookable-items';
import { index as bookingsIndex, show as bookingShow } from '@/routes/bookings';
import { index as contentIndex } from '@/routes/content';
import { index as inquiriesIndex, show as inquiryShow } from '@/routes/inquiries';
import { index as menusIndex } from '@/routes/menus';

interface StatProps {
    total_revenue: number;
    total_bookings: number;
    pending_bookings: number;
    confirmed_bookings: number;
    completed_bookings: number;
    upcoming_sessions: number;
    open_inquiries: number;
    active_programs: number;
}

interface BookingItem {
    id: number;
    reference: string;
    participant_name: string;
    participant_email: string;
    scheduled_at: string;
    amount: number;
    currency: string;
    status: string;
    payment_status: string;
    bookable_item?: {
        id: number;
        name: string;
    };
}

interface InquiryItem {
    id: number;
    name: string;
    email: string;
    subject: string;
    status: string;
    created_at: string;
}

interface ProgramItem {
    id: number;
    name: string;
    slug: string;
    price: number;
    duration_minutes: number;
    capacity: number;
}

const props = defineProps<{
    stats: StatProps;
    recentBookings: BookingItem[];
    recentInquiries: InquiryItem[];
    activePrograms: ProgramItem[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

function formatCurrency(amount: number, currency = 'GBP'): string {
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: currency || 'GBP',
    }).format(amount || 0);
}

function formatDate(dateStr: string): string {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatShortDate(dateStr: string): string {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
    });
}

function getBookingStatusClass(status: string): string {
    switch (status?.toLowerCase()) {
        case 'confirmed':
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200/60 dark:border-emerald-800/40';
        case 'completed':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400 border-blue-200/60 dark:border-blue-800/40';
        case 'pending':
            return 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200/60 dark:border-amber-800/40';
        case 'cancelled':
            return 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400 border-red-200/60 dark:border-red-800/40';
        default:
            return 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 border-neutral-200 dark:border-neutral-700';
    }
}

function getInquiryStatusClass(status: string): string {
    switch (status?.toLowerCase()) {
        case 'new':
            return 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-400 border-sky-200/60 dark:border-sky-800/40';
        case 'open':
        case 'in_progress':
            return 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200/60 dark:border-amber-800/40';
        case 'resolved':
        case 'closed':
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200/60 dark:border-emerald-800/40';
        default:
            return 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 border-neutral-200 dark:border-neutral-700';
    }
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header Banner -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100">
                    Dashboard Overview
                </h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Welcome back! Here is what's happening across TopGrade FC today.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Link
                    :href="createBookableItem()"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-neutral-900 px-3.5 py-2 text-xs font-semibold text-white shadow-xs transition-colors hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
                >
                    <Plus class="h-3.5 w-3.5" />
                    New Program
                </Link>
            </div>
        </div>

        <!-- 4 Metric Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Revenue Card -->
            <div
                class="relative overflow-hidden rounded-xl border border-neutral-200/80 bg-white p-5 shadow-xs dark:border-neutral-800 dark:bg-neutral-900"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider dark:text-neutral-400">
                        Total Revenue
                    </span>
                    <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <CreditCard class="h-4 w-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <div class="text-2xl font-extrabold text-neutral-900 dark:text-white">
                        {{ formatCurrency(stats?.total_revenue || 0) }}
                    </div>
                </div>
                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    From confirmed & paid bookings
                </p>
            </div>

            <!-- Total Bookings Card -->
            <div
                class="relative overflow-hidden rounded-xl border border-neutral-200/80 bg-white p-5 shadow-xs dark:border-neutral-800 dark:bg-neutral-900"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider dark:text-neutral-400">
                        Total Bookings
                    </span>
                    <div class="rounded-lg bg-blue-50 p-2 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                        <ClipboardList class="h-4 w-4" />
                    </div>
                </div>
                <div class="mt-3 text-2xl font-extrabold text-neutral-900 dark:text-white">
                    {{ stats?.total_bookings || 0 }}
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-neutral-500 dark:text-neutral-400">
                    <span class="font-medium text-amber-600 dark:text-amber-400">{{ stats?.pending_bookings || 0 }} pending</span>
                    <span>•</span>
                    <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ stats?.confirmed_bookings || 0 }} confirmed</span>
                </div>
            </div>

            <!-- Upcoming Sessions Card -->
            <div
                class="relative overflow-hidden rounded-xl border border-neutral-200/80 bg-white p-5 shadow-xs dark:border-neutral-800 dark:bg-neutral-900"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider dark:text-neutral-400">
                        Upcoming Sessions
                    </span>
                    <div class="rounded-lg bg-purple-50 p-2 text-purple-600 dark:bg-purple-950/50 dark:text-purple-400">
                        <CalendarCheck class="h-4 w-4" />
                    </div>
                </div>
                <div class="mt-3 text-2xl font-extrabold text-neutral-900 dark:text-white">
                    {{ stats?.upcoming_sessions || 0 }}
                </div>
                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Scheduled coaching sessions
                </p>
            </div>

            <!-- Open Inquiries Card -->
            <div
                class="relative overflow-hidden rounded-xl border border-neutral-200/80 bg-white p-5 shadow-xs dark:border-neutral-800 dark:bg-neutral-900"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider dark:text-neutral-400">
                        Open Inquiries
                    </span>
                    <div
                        class="rounded-lg p-2"
                        :class="stats?.open_inquiries > 0 ? 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400'"
                    >
                        <Inbox class="h-4 w-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <div class="text-2xl font-extrabold text-neutral-900 dark:text-white">
                        {{ stats?.open_inquiries || 0 }}
                    </div>
                    <span
                        v-if="stats?.open_inquiries > 0"
                        class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-900/60 dark:text-amber-300"
                    >
                        Needs attention
                    </span>
                </div>
                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Active customer contact forms
                </p>
            </div>
        </div>

        <!-- Quick Action Links Bar -->
        <div class="rounded-xl border border-neutral-200/80 bg-white p-4 shadow-xs dark:border-neutral-800 dark:bg-neutral-900">
            <h2 class="text-xs font-semibold uppercase tracking-wider text-neutral-400 dark:text-neutral-500">
                Quick Shortcuts
            </h2>
            <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-5">
                <Link
                    :href="bookingsIndex()"
                    class="flex items-center gap-2 rounded-lg border border-neutral-200/70 bg-neutral-50/50 p-2.5 text-xs font-medium text-neutral-700 transition-colors hover:border-neutral-300 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-800/40 dark:text-neutral-200 dark:hover:bg-neutral-800"
                >
                    <ClipboardList class="h-4 w-4 text-blue-500" />
                    <span>Manage Bookings</span>
                </Link>
                <Link
                    :href="bookableItemsIndex()"
                    class="flex items-center gap-2 rounded-lg border border-neutral-200/70 bg-neutral-50/50 p-2.5 text-xs font-medium text-neutral-700 transition-colors hover:border-neutral-300 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-800/40 dark:text-neutral-200 dark:hover:bg-neutral-800"
                >
                    <Calendar class="h-4 w-4 text-purple-500" />
                    <span>Training Programs</span>
                </Link>
                <Link
                    :href="inquiriesIndex()"
                    class="flex items-center gap-2 rounded-lg border border-neutral-200/70 bg-neutral-50/50 p-2.5 text-xs font-medium text-neutral-700 transition-colors hover:border-neutral-300 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-800/40 dark:text-neutral-200 dark:hover:bg-neutral-800"
                >
                    <Inbox class="h-4 w-4 text-amber-500" />
                    <span>View Inquiries</span>
                </Link>
                <Link
                    :href="contentIndex()"
                    class="flex items-center gap-2 rounded-lg border border-neutral-200/70 bg-neutral-50/50 p-2.5 text-xs font-medium text-neutral-700 transition-colors hover:border-neutral-300 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-800/40 dark:text-neutral-200 dark:hover:bg-neutral-800"
                >
                    <FileText class="h-4 w-4 text-emerald-500" />
                    <span>CMS Content</span>
                </Link>
                <Link
                    :href="menusIndex()"
                    class="flex items-center gap-2 rounded-lg border border-neutral-200/70 bg-neutral-50/50 p-2.5 text-xs font-medium text-neutral-700 transition-colors hover:border-neutral-300 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-800/40 dark:text-neutral-200 dark:hover:bg-neutral-800"
                >
                    <Menu class="h-4 w-4 text-sky-500" />
                    <span>Navigation Menus</span>
                </Link>
            </div>
        </div>

        <!-- Main Content Area: 2 Columns on Desktop -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Left Column: Recent Bookings (2-span) -->
            <div class="flex flex-col gap-6 lg:col-span-2">
                <div class="rounded-xl border border-neutral-200/80 bg-white shadow-xs dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between border-b border-neutral-200/80 p-5 dark:border-neutral-800">
                        <div>
                            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">
                                Recent Bookings
                            </h2>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                Latest player enrollments and session registrations
                            </p>
                        </div>
                        <Link
                            :href="bookingsIndex()"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-neutral-600 transition-colors hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white"
                        >
                            View all
                            <ArrowUpRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table v-if="recentBookings && recentBookings.length > 0" class="w-full text-left text-xs">
                            <thead class="bg-neutral-50/70 text-neutral-500 dark:bg-neutral-800/50 dark:text-neutral-400">
                                <tr>
                                    <th class="px-5 py-3 font-medium">Ref / Participant</th>
                                    <th class="px-5 py-3 font-medium">Program</th>
                                    <th class="px-5 py-3 font-medium">Scheduled</th>
                                    <th class="px-5 py-3 font-medium">Amount</th>
                                    <th class="px-5 py-3 font-medium">Status</th>
                                    <th class="px-5 py-3 font-end text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200/60 dark:divide-neutral-800/60">
                                <tr
                                    v-for="booking in recentBookings"
                                    :key="booking.id"
                                    class="transition-colors hover:bg-neutral-50/50 dark:hover:bg-neutral-800/30"
                                >
                                    <td class="px-5 py-3.5">
                                        <div class="font-semibold text-neutral-900 dark:text-white">
                                            {{ booking.participant_name }}
                                        </div>
                                        <div class="text-[11px] text-neutral-500 font-mono dark:text-neutral-400">
                                            {{ booking.reference }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-neutral-700 dark:text-neutral-300 font-medium">
                                        {{ booking.bookable_item?.name || 'General Training' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-neutral-600 dark:text-neutral-400">
                                        {{ formatDate(booking.scheduled_at) }}
                                    </td>
                                    <td class="px-5 py-3.5 font-semibold text-neutral-900 dark:text-white">
                                        {{ formatCurrency(booking.amount, booking.currency) }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span
                                            class="inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-medium capitalize"
                                            :class="getBookingStatusClass(booking.status)"
                                        >
                                            {{ booking.status }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <Link
                                            :href="bookingShow({ booking: booking.reference })"
                                            class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            Details
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-else class="p-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            No bookings registered yet.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Recent Inquiries & Active Programs -->
            <div class="flex flex-col gap-6 lg:col-span-1">
                <!-- Recent Inquiries Panel -->
                <div class="rounded-xl border border-neutral-200/80 bg-white shadow-xs dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between border-b border-neutral-200/80 p-4 dark:border-neutral-800">
                        <div>
                            <h2 class="text-sm font-semibold text-neutral-900 dark:text-white">
                                Customer Inquiries
                            </h2>
                            <p class="text-[11px] text-neutral-500 dark:text-neutral-400">
                                Latest form submissions
                            </p>
                        </div>
                        <Link
                            :href="inquiriesIndex()"
                            class="text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400"
                        >
                            View all
                        </Link>
                    </div>

                    <div v-if="recentInquiries && recentInquiries.length > 0" class="divide-y divide-neutral-200/60 dark:divide-neutral-800/60">
                        <div
                            v-for="inquiry in recentInquiries"
                            :key="inquiry.id"
                            class="p-4 transition-colors hover:bg-neutral-50/50 dark:hover:bg-neutral-800/30"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <span class="truncate text-xs font-semibold text-neutral-900 dark:text-white">
                                    {{ inquiry.name }}
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-medium capitalize shrink-0"
                                    :class="getInquiryStatusClass(inquiry.status)"
                                >
                                    {{ inquiry.status }}
                                </span>
                            </div>
                            <p class="mt-1 line-clamp-1 text-xs text-neutral-600 dark:text-neutral-300">
                                {{ inquiry.subject }}
                            </p>
                            <div class="mt-2 flex items-center justify-between text-[11px] text-neutral-400">
                                <span>{{ formatShortDate(inquiry.created_at) }}</span>
                                <Link
                                    :href="inquiryShow({ inquiry: inquiry.id })"
                                    class="font-medium text-blue-600 hover:underline dark:text-blue-400"
                                >
                                    Respond &rarr;
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-6 text-center text-xs text-neutral-500 dark:text-neutral-400">
                        No customer inquiries received.
                    </div>
                </div>

                <!-- Active Programs Quick View -->
                <div class="rounded-xl border border-neutral-200/80 bg-white p-4 shadow-xs dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between border-b border-neutral-200/80 pb-3 dark:border-neutral-800">
                        <h2 class="text-sm font-semibold text-neutral-900 dark:text-white">
                            Active Programs
                        </h2>
                        <Link
                            :href="bookableItemsIndex()"
                            class="text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400"
                        >
                            Manage
                        </Link>
                    </div>

                    <div v-if="activePrograms && activePrograms.length > 0" class="mt-3 space-y-2.5">
                        <div
                            v-for="prog in activePrograms"
                            :key="prog.id"
                            class="flex items-center justify-between rounded-lg border border-neutral-200/60 bg-neutral-50/50 p-2.5 dark:border-neutral-800 dark:bg-neutral-800/40"
                        >
                            <div class="truncate">
                                <div class="truncate text-xs font-semibold text-neutral-900 dark:text-white">
                                    {{ prog.name }}
                                </div>
                                <div class="mt-0.5 flex items-center gap-2 text-[11px] text-neutral-500 dark:text-neutral-400">
                                    <span>{{ prog.duration_minutes }} mins</span>
                                    <span>•</span>
                                    <span>Max {{ prog.capacity }} spots</span>
                                </div>
                            </div>
                            <div class="text-xs font-bold text-neutral-900 shrink-0 dark:text-white">
                                {{ formatCurrency(prog.price) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
