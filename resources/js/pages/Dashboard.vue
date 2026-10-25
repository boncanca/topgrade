<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    Activity,
    ArrowUpRight,
    CalendarCheck,
    ChevronDown,
    ChevronRight,
    FileText,
    History,
    Image as ImageIcon,
    Newspaper,
    PenSquare,
    Plus,
    Upload,
    Users,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import { dashboard } from '@/routes';
import { index as bookableItemsIndex } from '@/routes/bookable-items';
import { index as bookingsIndex } from '@/routes/bookings';
import {
    create as contentCreate,
    edit as contentEdit,
    index as contentIndex,
} from '@/routes/content';
import { index as inquiriesIndex } from '@/routes/inquiries';

interface UpcomingMatch {
    days_left: string;
    opponent: string;
    date?: string;
    kickoff?: string;
    venue?: string;
    competition?: string;
}

interface StatProps {
    pages_count?: number;
    news_count?: number;
    gallery_items_count?: number;
    upcoming_match?: UpcomingMatch;
    total_revenue?: number;
    total_bookings?: number;
    pending_bookings?: number;
    confirmed_bookings?: number;
    completed_bookings?: number;
    upcoming_sessions?: number;
    open_inquiries?: number;
    active_programs?: number;
}

interface RecentUpdateItem {
    id: number;
    title: string;
    slug: string;
    category?: string;
    featured_image?: string;
    published_at?: string;
    published_human?: string;
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

defineProps<{
    stats: StatProps;
    recentUpdates?: RecentUpdateItem[];
    recentBookings?: BookingItem[];
    recentInquiries?: InquiryItem[];
    activePrograms?: ProgramItem[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Overview',
                href: dashboard(),
            },
        ],
    },
});

const page = usePage();
const userName = computed(() => {
    const full = (page.props.auth as any)?.user?.name || 'Tom';

    return full.split(' ')[0] || 'Tom';
});

const showOperations = ref(true);

function formatCurrency(amount: number, currency = 'GBP'): string {
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: currency || 'GBP',
    }).format(amount || 0);
}
</script>

<template>
    <Head title="Overview" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8">
        <!-- 1. Header Banner -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold tracking-tight text-neutral-900 sm:text-3xl dark:text-white"
                >
                    Overview
                </h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    Welcome back, {{ userName }}. Club updates, activities, and
                    operational summary.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="/"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="shadow-2xs inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-white px-3.5 py-2 text-xs font-semibold text-neutral-800 transition-all hover:border-purple-400 hover:text-purple-600 dark:border-white/10 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:border-purple-500/50 dark:hover:text-purple-300"
                >
                    <span>View Website</span>
                    <ArrowUpRight class="size-3.5 text-neutral-400" />
                </a>
            </div>
        </div>

        <!-- 2. Core Stat Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Pages Card -->
            <Link
                :href="contentIndex({ query: { type: 'page' } })"
                class="shadow-2xs group relative overflow-hidden rounded-xl border border-neutral-200/90 bg-white p-5 transition-all duration-200 hover:border-purple-400/80 hover:shadow-md hover:shadow-purple-900/10 dark:border-white/10 dark:bg-neutral-900/90 dark:hover:border-purple-500/60 dark:hover:shadow-purple-950/30"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-11 items-center justify-center rounded-lg border border-purple-500/20 bg-purple-50 text-purple-600 transition-colors group-hover:bg-purple-600 group-hover:text-white dark:border-purple-500/30 dark:bg-purple-950/50 dark:text-purple-300 dark:group-hover:bg-purple-600 dark:group-hover:text-white"
                    >
                        <FileText class="size-5" />
                    </div>
                    <div>
                        <div
                            class="text-xs font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Pages
                        </div>
                        <div
                            class="text-2xl font-extrabold tracking-tight text-neutral-900 dark:text-white"
                        >
                            {{ stats.pages_count ?? 8 }}
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-1.5 text-xs">
                    <span
                        class="inline-block size-1.5 rounded-full bg-emerald-500"
                    />
                    <span
                        class="font-medium text-emerald-600 dark:text-emerald-400"
                        >Live in Navigation</span
                    >
                </div>
            </Link>

            <!-- Articles Card -->
            <Link
                :href="contentIndex({ query: { type: 'article' } })"
                class="shadow-2xs group relative overflow-hidden rounded-xl border border-neutral-200/90 bg-white p-5 transition-all duration-200 hover:border-purple-400/80 hover:shadow-md hover:shadow-purple-900/10 dark:border-white/10 dark:bg-neutral-900/90 dark:hover:border-purple-500/60 dark:hover:shadow-purple-950/30"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-11 items-center justify-center rounded-lg border border-purple-500/20 bg-purple-50 text-purple-600 transition-colors group-hover:bg-purple-600 group-hover:text-white dark:border-purple-500/30 dark:bg-purple-950/50 dark:text-purple-300 dark:group-hover:bg-purple-600 dark:group-hover:text-white"
                    >
                        <Newspaper class="size-5" />
                    </div>
                    <div>
                        <div
                            class="text-xs font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Articles & News
                        </div>
                        <div
                            class="text-2xl font-extrabold tracking-tight text-neutral-900 dark:text-white"
                        >
                            {{ stats.news_count ?? 12 }}
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-1.5 text-xs">
                    <span
                        class="inline-block size-1.5 rounded-full bg-emerald-500"
                    />
                    <span
                        class="font-medium text-emerald-600 dark:text-emerald-400"
                        >Published</span
                    >
                </div>
            </Link>

            <!-- Galleries Card -->
            <Link
                href="/dashboard/galleries"
                class="shadow-2xs group relative overflow-hidden rounded-xl border border-neutral-200/90 bg-white p-5 transition-all duration-200 hover:border-purple-400/80 hover:shadow-md hover:shadow-purple-900/10 dark:border-white/10 dark:bg-neutral-900/90 dark:hover:border-purple-500/60 dark:hover:shadow-purple-950/30"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-11 items-center justify-center rounded-lg border border-purple-500/20 bg-purple-50 text-purple-600 transition-colors group-hover:bg-purple-600 group-hover:text-white dark:border-purple-500/30 dark:bg-purple-950/50 dark:text-purple-300 dark:group-hover:bg-purple-600 dark:group-hover:text-white"
                    >
                        <ImageIcon class="size-5" />
                    </div>
                    <div>
                        <div
                            class="text-xs font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Galleries
                        </div>
                        <div
                            class="text-2xl font-extrabold tracking-tight text-neutral-900 dark:text-white"
                        >
                            {{ stats.gallery_items_count ?? 45 }}
                        </div>
                    </div>
                </div>
                <div
                    class="mt-3 text-xs text-neutral-500 dark:text-neutral-400"
                >
                    Photos & Match Albums
                </div>
            </Link>

            <!-- Bookings & Inquiries Card -->
            <Link
                :href="bookingsIndex()"
                class="shadow-2xs group relative overflow-hidden rounded-xl border border-neutral-200/90 bg-white p-5 transition-all duration-200 hover:border-purple-400/80 hover:shadow-md hover:shadow-purple-900/10 dark:border-white/10 dark:bg-neutral-900/90 dark:hover:border-purple-500/60 dark:hover:shadow-purple-950/30"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-11 items-center justify-center rounded-lg border border-purple-500/20 bg-purple-50 text-purple-600 transition-colors group-hover:bg-purple-600 group-hover:text-white dark:border-purple-500/30 dark:bg-purple-950/50 dark:text-purple-300 dark:group-hover:bg-purple-600 dark:group-hover:text-white"
                    >
                        <CalendarCheck class="size-5" />
                    </div>
                    <div>
                        <div
                            class="text-xs font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Total Bookings
                        </div>
                        <div
                            class="text-2xl font-extrabold tracking-tight text-neutral-900 dark:text-white"
                        >
                            {{ stats.total_bookings ?? 0 }}
                        </div>
                    </div>
                </div>
                <div
                    class="mt-3 flex items-center gap-1.5 text-xs font-medium text-amber-600 dark:text-amber-400"
                >
                    <span
                        >{{ stats.pending_bookings ?? 0 }} pending
                        approval</span
                    >
                </div>
            </Link>
        </div>

        <!-- 3. Quick Actions & Relevant Updates -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <!-- Left: Quick Actions (5 cols) -->
            <div class="flex flex-col gap-4 lg:col-span-5">
                <div class="flex items-center justify-between">
                    <h2
                        class="text-base font-bold text-neutral-900 dark:text-white"
                    >
                        Quick Actions
                    </h2>
                </div>

                <div class="flex flex-col gap-3">
                    <!-- Action 1: Create Article (Primary Purple Card) -->
                    <Link
                        :href="contentCreate({ query: { type: 'article' } })"
                        class="group relative flex items-center gap-3.5 overflow-hidden rounded-xl bg-[#7c3aed] p-4 text-white shadow-md shadow-purple-950/30 transition-all duration-200 hover:bg-[#6d28d9] hover:shadow-lg hover:shadow-purple-950/50"
                    >
                        <div
                            class="backdrop-blur-xs flex size-10 shrink-0 items-center justify-center rounded-lg bg-white/20 text-white transition-transform group-hover:scale-105"
                        >
                            <PenSquare class="size-5" />
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-semibold tracking-wide">
                                Create New Article
                            </div>
                            <div class="text-xs text-white/80">
                                Publish news, match reports & club announcements
                            </div>
                        </div>
                        <ChevronRight
                            class="size-4 text-white/60 transition-transform group-hover:translate-x-1"
                        />
                    </Link>

                    <!-- Action 2: Add Photos / Gallery -->
                    <Link
                        href="/dashboard/galleries/create"
                        class="shadow-2xs group relative flex items-center gap-3.5 rounded-xl border border-neutral-200/90 bg-white p-4 transition-all duration-200 hover:border-purple-400 hover:shadow-sm dark:border-white/10 dark:bg-neutral-900/90 dark:hover:border-purple-500/50"
                    >
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-purple-500/20 bg-purple-50 text-purple-600 transition-colors group-hover:bg-purple-600 group-hover:text-white dark:border-purple-500/30 dark:bg-purple-950/40 dark:text-purple-300 dark:group-hover:bg-purple-600 dark:group-hover:text-white"
                        >
                            <Upload class="size-5" />
                        </div>
                        <div class="flex-1">
                            <div
                                class="text-sm font-semibold text-neutral-900 dark:text-white"
                            >
                                Add Photos to Gallery
                            </div>
                            <div
                                class="text-xs text-neutral-500 dark:text-neutral-400"
                            >
                                Upload new match action and training moments
                            </div>
                        </div>
                        <ChevronRight
                            class="size-4 text-neutral-400 transition-transform group-hover:translate-x-1"
                        />
                    </Link>

                    <!-- Action 3: Manage Users & Roles -->
                    <Link
                        href="/dashboard/users"
                        class="shadow-2xs group relative flex items-center gap-3.5 rounded-xl border border-neutral-200/90 bg-white p-4 transition-all duration-200 hover:border-purple-400 hover:shadow-sm dark:border-white/10 dark:bg-neutral-900/90 dark:hover:border-purple-500/50"
                    >
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-purple-500/20 bg-purple-50 text-purple-600 transition-colors group-hover:bg-purple-600 group-hover:text-white dark:border-purple-500/30 dark:bg-purple-950/40 dark:text-purple-300 dark:group-hover:bg-purple-600 dark:group-hover:text-white"
                        >
                            <Users class="size-5" />
                        </div>
                        <div class="flex-1">
                            <div
                                class="text-sm font-semibold text-neutral-900 dark:text-white"
                            >
                                Users & Staff Roles
                            </div>
                            <div
                                class="text-xs text-neutral-500 dark:text-neutral-400"
                            >
                                Assign administrator, editor, and coach roles
                            </div>
                        </div>
                        <ChevronRight
                            class="size-4 text-neutral-400 transition-transform group-hover:translate-x-1"
                        />
                    </Link>

                    <!-- Action 4: Audit & Error Logs -->
                    <Link
                        href="/dashboard/logs"
                        class="shadow-2xs group relative flex items-center gap-3.5 rounded-xl border border-neutral-200/90 bg-white p-4 transition-all duration-200 hover:border-purple-400 hover:shadow-sm dark:border-white/10 dark:bg-neutral-900/90 dark:hover:border-purple-500/50"
                    >
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-purple-500/20 bg-purple-50 text-purple-600 transition-colors group-hover:bg-purple-600 group-hover:text-white dark:border-purple-500/30 dark:bg-purple-950/40 dark:text-purple-300 dark:group-hover:bg-purple-600 dark:group-hover:text-white"
                        >
                            <History class="size-5" />
                        </div>
                        <div class="flex-1">
                            <div
                                class="text-sm font-semibold text-neutral-900 dark:text-white"
                            >
                                Audit Trail & Logs
                            </div>
                            <div
                                class="text-xs text-neutral-500 dark:text-neutral-400"
                            >
                                Review recent user activity and system traces
                            </div>
                        </div>
                        <ChevronRight
                            class="size-4 text-neutral-400 transition-transform group-hover:translate-x-1"
                        />
                    </Link>
                </div>
            </div>

            <!-- Right: Relevant Updates Only (7 cols) -->
            <div class="flex flex-col gap-4 lg:col-span-7">
                <div class="flex items-center justify-between">
                    <div>
                        <h2
                            class="text-base font-bold text-neutral-900 dark:text-white"
                        >
                            Relevant Updates
                        </h2>
                        <p
                            class="text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            Active publications and recent announcements
                        </p>
                    </div>
                    <Link
                        :href="contentIndex({ query: { type: 'article' } })"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300"
                    >
                        <span>All Articles</span>
                        <ChevronRight class="size-3.5" />
                    </Link>
                </div>

                <!-- Recent Updates List -->
                <div
                    v-if="recentUpdates && recentUpdates.length > 0"
                    class="flex flex-col gap-2.5"
                >
                    <Link
                        v-for="item in recentUpdates"
                        :key="item.id"
                        :href="contentEdit(item.id)"
                        class="shadow-2xs group flex items-center gap-3.5 rounded-xl border border-neutral-200/90 bg-white p-3 transition-all duration-200 hover:border-purple-400 hover:shadow-sm dark:border-white/10 dark:bg-neutral-900/90 dark:hover:border-purple-500/50"
                    >
                        <div
                            class="relative size-14 shrink-0 overflow-hidden rounded-lg bg-neutral-100 dark:bg-neutral-800"
                        >
                            <img
                                :src="
                                    item.featured_image ||
                                    '/images/club/player-match-action-topgrade-london-fc.webp'
                                "
                                :alt="item.title"
                                class="size-full object-cover transition-transform duration-300 group-hover:scale-105"
                                loading="lazy"
                            />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span
                                    v-if="item.category"
                                    class="inline-flex rounded-md bg-purple-50 px-2 py-0.5 text-[10px] font-semibold text-purple-700 dark:bg-purple-950/60 dark:text-purple-300"
                                >
                                    {{ item.category }}
                                </span>
                            </div>
                            <div
                                class="mt-1 truncate text-sm font-semibold text-neutral-900 transition-colors group-hover:text-purple-600 dark:text-white dark:group-hover:text-purple-400"
                            >
                                {{ item.title }}
                            </div>
                            <div
                                class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400"
                            >
                                {{
                                    item.published_human || 'Published recently'
                                }}
                            </div>
                        </div>

                        <div
                            class="hidden shrink-0 items-center gap-1.5 sm:flex"
                        >
                            <span
                                class="rounded-md border border-neutral-200 bg-neutral-50 px-2.5 py-1 text-xs font-medium text-neutral-600 transition-colors group-hover:border-purple-400 group-hover:text-purple-600 dark:border-neutral-800 dark:bg-neutral-800/80 dark:text-neutral-300 dark:group-hover:border-purple-500/50 dark:group-hover:text-purple-300"
                            >
                                Edit
                            </span>
                        </div>
                    </Link>
                </div>

                <div
                    v-else
                    class="flex flex-col items-center justify-center rounded-xl border border-dashed border-neutral-300 bg-white/50 p-8 text-center dark:border-white/10 dark:bg-neutral-900/50"
                >
                    <Newspaper class="size-8 text-neutral-400" />
                    <p
                        class="mt-2 text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        No recent updates published
                    </p>
                    <Link
                        :href="contentCreate({ query: { type: 'article' } })"
                        class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-purple-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-purple-700"
                    >
                        <Plus class="size-3.5" />
                        Create first article
                    </Link>
                </div>
            </div>
        </div>

        <!-- 4. Operations, Activities & Bookings Summary -->
        <div
            class="shadow-2xs mt-4 rounded-xl border border-neutral-200/90 bg-white dark:border-white/10 dark:bg-neutral-900/90"
        >
            <button
                type="button"
                @click="showOperations = !showOperations"
                class="flex w-full items-center justify-between p-4 text-left font-semibold text-neutral-900 dark:text-white"
            >
                <div class="flex items-center gap-2.5">
                    <Activity
                        class="size-4.5 text-purple-600 dark:text-purple-400"
                    />
                    <span class="text-sm font-bold"
                        >Activities, Bookings & Financial Status</span
                    >
                    <span
                        class="rounded-full bg-purple-100 px-2 py-0.5 text-[11px] font-medium text-purple-700 dark:bg-purple-950/60 dark:text-purple-300"
                    >
                        {{ stats.total_bookings || 0 }} Bookings
                    </span>
                </div>
                <div
                    class="flex items-center gap-2 text-xs text-neutral-500 dark:text-neutral-400"
                >
                    <span>{{
                        showOperations ? 'Hide Summary' : 'Show Summary'
                    }}</span>
                    <ChevronDown
                        class="size-4 transition-transform duration-200"
                        :class="showOperations ? 'rotate-180' : ''"
                    />
                </div>
            </button>

            <div
                v-if="showOperations"
                class="border-t border-neutral-200/80 p-5 dark:border-white/10"
            >
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    <div
                        class="rounded-lg border border-neutral-200/60 bg-neutral-50/60 p-3.5 dark:border-neutral-800 dark:bg-neutral-800/40"
                    >
                        <span
                            class="text-xs font-medium text-neutral-500 dark:text-neutral-400"
                            >Total Revenue</span
                        >
                        <div
                            class="mt-1 text-xl font-bold text-neutral-900 dark:text-white"
                        >
                            {{ formatCurrency(stats.total_revenue || 0) }}
                        </div>
                    </div>

                    <div
                        class="rounded-lg border border-neutral-200/60 bg-neutral-50/60 p-3.5 dark:border-neutral-800 dark:bg-neutral-800/40"
                    >
                        <span
                            class="text-xs font-medium text-neutral-500 dark:text-neutral-400"
                            >Total Bookings</span
                        >
                        <div
                            class="mt-1 text-xl font-bold text-neutral-900 dark:text-white"
                        >
                            {{ stats.total_bookings || 0 }}
                        </div>
                        <div
                            class="mt-1 text-[11px] text-amber-600 dark:text-amber-400"
                        >
                            {{ stats.pending_bookings || 0 }} pending review
                        </div>
                    </div>

                    <div
                        class="rounded-lg border border-neutral-200/60 bg-neutral-50/60 p-3.5 dark:border-neutral-800 dark:bg-neutral-800/40"
                    >
                        <span
                            class="text-xs font-medium text-neutral-500 dark:text-neutral-400"
                            >Upcoming Activity Sessions</span
                        >
                        <div
                            class="mt-1 text-xl font-bold text-neutral-900 dark:text-white"
                        >
                            {{ stats.upcoming_sessions || 0 }}
                        </div>
                    </div>

                    <div
                        class="rounded-lg border border-neutral-200/60 bg-neutral-50/60 p-3.5 dark:border-neutral-800 dark:bg-neutral-800/40"
                    >
                        <span
                            class="text-xs font-medium text-neutral-500 dark:text-neutral-400"
                            >Customer Inquiries</span
                        >
                        <div
                            class="mt-1 text-xl font-bold text-neutral-900 dark:text-white"
                        >
                            {{ stats.open_inquiries || 0 }}
                        </div>
                        <div class="mt-1 text-[11px] text-neutral-400">
                            Open messages
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <Link
                        :href="bookingsIndex()"
                        class="text-xs font-semibold text-purple-600 hover:underline dark:text-purple-400"
                    >
                        View all bookings &rarr;
                    </Link>
                    <span class="text-neutral-300 dark:text-neutral-700"
                        >•</span
                    >
                    <Link
                        :href="inquiriesIndex()"
                        class="text-xs font-semibold text-purple-600 hover:underline dark:text-purple-400"
                    >
                        View all inquiries &rarr;
                    </Link>
                    <span class="text-neutral-300 dark:text-neutral-700"
                        >•</span
                    >
                    <Link
                        :href="bookableItemsIndex()"
                        class="text-xs font-semibold text-purple-600 hover:underline dark:text-purple-400"
                    >
                        Manage activities &rarr;
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
