<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Clock,
    MapPin,
    Users,
    CheckCircle2,
    AlertCircle,
} from '@lucide/vue';
import { ref, computed, watch } from 'vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface Activity {
    id: number;
    name: string;
    slug: string;
    description: string;
    duration_minutes: number;
    location: string;
    price: string;
    currency: string;
    capacity: number;
}

interface Schedule {
    id: number;
    starts_at: string;
    ends_at: string;
    capacity: number;
    location: string;
    available_spots: number;
}

const props = defineProps<{
    activity: Activity;
    schedules: Schedule[];
}>();

defineOptions({
    layout: PublicLayout,
});

const form = ref({
    bookable_item_id: props.activity.id,
    schedule_id:
        props.schedules.length > 0 ? props.schedules[0].id.toString() : '',
    participant_name: '',
    participant_email: '',
    participant_phone: '',
    timezone:
        Intl.DateTimeFormat().resolvedOptions().timeZone || 'Europe/London',
    notes: '',
});

watch(
    () => props.schedules,
    (schedules: Schedule[]) => {
        if (schedules && schedules.length > 0 && !form.value.schedule_id) {
            form.value.schedule_id = schedules[0].id.toString();
        }
    },
    { immediate: true },
);

function selectSchedule(id: number | string) {
    form.value.schedule_id = id.toString();
}

const processing = ref(false);
const submitted = ref(false);

const isTrial = computed(() => {
    return (
        props.activity.slug.includes('trial') ||
        props.activity.price === '0' ||
        props.activity.price === '0.00'
    );
});

const formErrors = ref<Record<string, string>>({});

function submit() {
    processing.value = true;
    formErrors.value = {};

    router.post('/bookings', form.value, {
        onSuccess: () => {
            submitted.value = true;
            processing.value = false;
        },
        onError: (errors) => {
            formErrors.value = errors;
            processing.value = false;
        },
    });
}

function formatScheduleDate(dateStr: string): string {
    const date = new Date(dateStr);

    return new Intl.DateTimeFormat('en-GB', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
    }).format(date);
}

function formatScheduleTime(startsAt: string, endsAt: string): string {
    const start = new Date(startsAt);
    const end = new Date(endsAt);
    const startFormatted = new Intl.DateTimeFormat('en-GB', {
        hour: '2-digit',
        minute: '2-digit',
    }).format(start);
    const endFormatted = new Intl.DateTimeFormat('en-GB', {
        hour: '2-digit',
        minute: '2-digit',
    }).format(end);

    return `${startFormatted} – ${endFormatted}`;
}
</script>

<template>
    <SeoHead
        :title="`${activity.name} | TopGrade London FC`"
        :description="activity.description"
        :path="`/bookings/${activity.slug}`"
    />

    <div class="bg-tg-bg text-tg-text min-h-screen">
        <!-- Hero Section with Background Photography -->
        <section
            class="border-tg-border relative flex min-h-[36vh] items-end overflow-hidden border-b pb-12 pt-24 sm:min-h-[40vh] sm:pt-28"
        >
            <!-- Background Image with Solid Dark Contrast -->
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="
                    background-image: url('/images/club/topgrade-london-fc-floodlit-training-pitch.webp');
                "
            />
            <div
                class="from-tg-bg via-tg-bg/85 absolute inset-0 bg-gradient-to-t to-transparent"
            />

            <div
                class="relative z-10 mx-auto w-full max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8"
            >
                <!-- Navigation Link -->
                <Link
                    href="/bookings"
                    class="tg-btn ghost inline-flex items-center gap-2 px-3 py-1.5 text-xs"
                >
                    <ArrowLeft class="h-3.5 w-3.5" />
                    <span>Back to Bookings</span>
                </Link>

                <div class="flex flex-wrap items-center gap-2.5">
                    <span
                        class="rounded-xs bg-tg-bg-deep border-tg-border text-tg-text-muted border px-2.5 py-1 text-xs font-semibold"
                    >
                        {{ activity.duration_minutes }} Minutes
                    </span>
                    <span
                        class="rounded-xs bg-tg-bg-deep border-tg-border text-tg-text-muted flex items-center gap-1 border px-2.5 py-1 text-xs font-semibold"
                    >
                        <Users class="text-tg-accent h-3.5 w-3.5" />
                        <span>Max {{ activity.capacity }} Players</span>
                    </span>
                    <span
                        v-if="isTrial"
                        class="rounded-xs bg-tg-accent text-tg-text-strong px-3 py-1 text-xs font-bold uppercase tracking-wider"
                    >
                        Free Trial
                    </span>
                    <span
                        v-else
                        class="rounded-xs bg-tg-bg-deep border-tg-border text-tg-text-muted border px-3 py-1 text-xs font-semibold uppercase tracking-wider"
                    >
                        Club Training
                    </span>
                </div>

                <h1
                    class="text-tg-text-strong text-3xl font-normal uppercase tracking-tight sm:text-5xl"
                    style="font-family: var(--tg-display)"
                >
                    {{ activity.name }}
                </h1>
            </div>
        </section>

        <!-- Main Content Area -->
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid items-start gap-10 lg:grid-cols-3">
                <!-- Activity Information & Overview -->
                <div class="space-y-10 lg:col-span-2">
                    <!-- Quick Stats Grid -->
                    <div
                        class="bg-tg-bg-deep/80 border-tg-border grid grid-cols-3 gap-4 rounded-sm border p-6"
                    >
                        <div class="space-y-1">
                            <div
                                class="text-tg-accent flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider"
                            >
                                <Clock class="h-3.5 w-3.5" />
                                <span>Duration</span>
                            </div>
                            <div class="text-tg-text-strong text-lg font-bold">
                                {{ activity.duration_minutes }} mins
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div
                                class="text-tg-accent flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider"
                            >
                                <MapPin class="h-3.5 w-3.5" />
                                <span>Location</span>
                            </div>
                            <div
                                class="text-tg-text-strong truncate text-sm font-bold"
                            >
                                {{ activity.location }}
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div
                                class="text-tg-accent flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider"
                            >
                                <Users class="h-3.5 w-3.5" />
                                <span>Group Size</span>
                            </div>
                            <div class="text-tg-text-strong text-lg font-bold">
                                {{ activity.capacity }} Max
                            </div>
                        </div>
                    </div>

                    <!-- Program Overview -->
                    <div class="space-y-3">
                        <span
                            class="text-tg-accent block text-xs font-bold uppercase tracking-widest"
                            >Details</span
                        >
                        <h2
                            class="text-tg-text-strong text-2xl font-normal uppercase"
                            style="font-family: var(--tg-display)"
                        >
                            Programme Overview
                        </h2>
                        <p
                            class="text-tg-text text-sm leading-relaxed sm:text-base"
                        >
                            {{ activity.description }}
                        </p>
                    </div>

                    <!-- Included Features -->
                    <div
                        class="bg-tg-bg-deep/80 border-tg-border space-y-6 rounded-sm border p-6 sm:p-8"
                    >
                        <h3
                            class="text-tg-text-strong text-xl font-normal uppercase"
                            style="font-family: var(--tg-display)"
                        >
                            What's Included in This Session
                        </h3>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div
                                class="text-tg-text flex items-start gap-3 text-sm"
                            >
                                <CheckCircle2
                                    class="text-tg-accent mt-0.5 h-4 w-4 shrink-0"
                                />
                                <span
                                    >Dedicated club coaches and training
                                    staff</span
                                >
                            </div>
                            <div
                                class="text-tg-text flex items-start gap-3 text-sm"
                            >
                                <CheckCircle2
                                    class="text-tg-accent mt-0.5 h-4 w-4 shrink-0"
                                />
                                <span
                                    >Technical ball mastery & tactical match
                                    play</span
                                >
                            </div>
                            <div
                                class="text-tg-text flex items-start gap-3 text-sm"
                            >
                                <CheckCircle2
                                    class="text-tg-accent mt-0.5 h-4 w-4 shrink-0"
                                />
                                <span
                                    >Quality pitch and training equipment
                                    setup</span
                                >
                            </div>
                            <div
                                class="text-tg-text flex items-start gap-3 text-sm"
                            >
                                <CheckCircle2
                                    class="text-tg-accent mt-0.5 h-4 w-4 shrink-0"
                                />
                                <span
                                    >Post-session feedback & player
                                    evaluation</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Venue Details -->
                    <div
                        class="bg-tg-bg-deep/80 border-tg-border space-y-3 rounded-sm border p-6 sm:p-8"
                    >
                        <div class="flex items-center gap-2">
                            <MapPin class="text-tg-accent h-4 w-4" />
                            <h3
                                class="text-tg-text-strong text-xl font-normal uppercase"
                                style="font-family: var(--tg-display)"
                            >
                                Venue & Facility
                            </h3>
                        </div>
                        <p class="text-tg-text-muted text-sm leading-relaxed">
                            Sessions take place on our high-quality pitches at
                            <strong class="text-tg-text-strong">{{
                                activity.location
                            }}</strong
                            >. Changing rooms, parking, and parent viewing areas
                            are available on site.
                        </p>
                    </div>
                </div>

                <!-- Sticky Booking Sidebar Form -->
                <div
                    class="bg-tg-bg-deep border-tg-border sticky top-24 space-y-6 rounded-sm border p-6 shadow-xl sm:p-8"
                >
                    <div
                        class="border-tg-border flex items-baseline justify-between border-b pb-5"
                    >
                        <div>
                            <span
                                class="text-tg-text-muted text-xs font-semibold uppercase tracking-wider"
                                >Registration</span
                            >
                            <div
                                class="text-tg-text-strong mt-0.5 text-2xl font-bold"
                            >
                                {{ isTrial ? 'Free Trial' : 'Club Training' }}
                            </div>
                        </div>
                        <span class="text-tg-text-muted text-xs">{{
                            isTrial ? 'No fee required' : 'Session booking'
                        }}</span>
                    </div>

                    <!-- Success State -->
                    <div
                        v-if="submitted"
                        class="bg-tg-bg border-tg-accent space-y-3 rounded-sm border p-6 text-center"
                    >
                        <CheckCircle2
                            class="text-tg-accent mx-auto h-12 w-12"
                        />
                        <h4
                            class="text-tg-text-strong text-xl font-normal uppercase"
                            style="font-family: var(--tg-display)"
                        >
                            Booking Confirmed!
                        </h4>
                        <p class="text-tg-text-muted text-xs">
                            We have received your registration. Check your email
                            for session details and pitch directions.
                        </p>
                        <button
                            type="button"
                            class="tg-btn ghost mt-4 w-full justify-center text-xs"
                            @click="submitted = false"
                        >
                            Book Another Session
                        </button>
                    </div>

                    <!-- Booking Form -->
                    <form v-else class="space-y-5" @submit.prevent="submit">
                        <!-- Top Error Alert -->
                        <div
                            v-if="Object.keys(formErrors).length > 0"
                            class="rounded-xs flex items-center gap-2.5 border border-rose-500/40 bg-rose-500/10 p-3.5 text-xs text-rose-300"
                        >
                            <AlertCircle
                                class="h-4 w-4 shrink-0 text-rose-400"
                            />
                            <span
                                >Please review the highlighted booking details
                                below.</span
                            >
                        </div>

                        <!-- Choose Schedule Slot -->
                        <div>
                            <label
                                class="text-tg-text-muted mb-2.5 block text-xs font-semibold uppercase tracking-wider"
                            >
                                Select Session Date & Time
                                <span class="text-tg-accent">*</span>
                            </label>

                            <div
                                v-if="schedules.length === 0"
                                class="bg-tg-bg border-tg-border text-tg-highlight flex items-center gap-2 rounded-sm border p-4 text-xs"
                            >
                                <AlertCircle
                                    class="text-tg-highlight h-4 w-4 shrink-0"
                                />
                                <span
                                    >No upcoming slots available currently.
                                    Check back soon or contact us.</span
                                >
                            </div>

                            <div
                                v-else
                                class="max-h-52 space-y-2 overflow-y-auto pr-1"
                            >
                                <label
                                    v-for="schedule in schedules"
                                    :key="schedule.id"
                                    @click="selectSchedule(schedule.id)"
                                    :class="[
                                        String(form.schedule_id) ===
                                        String(schedule.id)
                                            ? 'border-tg-accent bg-tg-accent/15 ring-tg-accent ring-1'
                                            : formErrors.schedule_id
                                              ? 'border-rose-500/80 bg-rose-500/5'
                                              : 'border-tg-border bg-tg-bg/70 hover:border-tg-border-strong',
                                        'rounded-xs flex cursor-pointer items-center justify-between border p-3 transition-all',
                                    ]"
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            v-model="form.schedule_id"
                                            :value="schedule.id.toString()"
                                            type="radio"
                                            name="schedule"
                                            class="h-4 w-4 accent-pink-500"
                                            required
                                        />
                                        <div>
                                            <div
                                                class="text-tg-text-strong text-xs font-bold"
                                            >
                                                {{
                                                    formatScheduleDate(
                                                        schedule.starts_at,
                                                    )
                                                }}
                                            </div>
                                            <div
                                                class="text-tg-text-muted mt-0.5 text-[11px]"
                                            >
                                                {{
                                                    formatScheduleTime(
                                                        schedule.starts_at,
                                                        schedule.ends_at,
                                                    )
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        :class="[
                                            schedule.available_spots > 0
                                                ? 'text-tg-accent bg-tg-bg border-tg-accent/30'
                                                : 'bg-tg-bg border-rose-500/30 text-rose-400',
                                            'rounded-xs border px-2 py-0.5 text-[10px] font-semibold',
                                        ]"
                                    >
                                        {{ schedule.available_spots }} left
                                    </span>
                                </label>
                            </div>
                            <p
                                v-if="formErrors.schedule_id"
                                class="mt-1 text-xs font-medium text-rose-400"
                            >
                                {{ formErrors.schedule_id }}
                            </p>
                        </div>

                        <!-- Participant Name -->
                        <div>
                            <label
                                class="text-tg-text-muted mb-1 block text-xs font-semibold uppercase tracking-wider"
                            >
                                Participant / Parent Name
                                <span class="text-tg-accent">*</span>
                            </label>
                            <input
                                v-model="form.participant_name"
                                type="text"
                                required
                                :class="[
                                    formErrors.participant_name
                                        ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500'
                                        : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'rounded-xs bg-tg-bg text-tg-text-strong placeholder:text-tg-text-muted/60 w-full px-3.5 py-2.5 text-sm outline-none transition-colors focus:ring-1 sm:text-base',
                                ]"
                                placeholder="e.g. Alex Smith"
                            />
                            <p
                                v-if="formErrors.participant_name"
                                class="mt-1 text-xs font-medium text-rose-400"
                            >
                                {{ formErrors.participant_name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label
                                class="text-tg-text-muted mb-1 block text-xs font-semibold uppercase tracking-wider"
                            >
                                Email Address
                                <span class="text-tg-accent">*</span>
                            </label>
                            <input
                                v-model="form.participant_email"
                                type="email"
                                required
                                :class="[
                                    formErrors.participant_email
                                        ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500'
                                        : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'rounded-xs bg-tg-bg text-tg-text-strong placeholder:text-tg-text-muted/60 w-full px-3.5 py-2.5 text-sm outline-none transition-colors focus:ring-1 sm:text-base',
                                ]"
                                placeholder="alex@example.com"
                            />
                            <p
                                v-if="formErrors.participant_email"
                                class="mt-1 text-xs font-medium text-rose-400"
                            >
                                {{ formErrors.participant_email }}
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label
                                class="text-tg-text-muted mb-1 block text-xs font-semibold uppercase tracking-wider"
                            >
                                Phone Number (optional)
                            </label>
                            <input
                                v-model="form.participant_phone"
                                type="tel"
                                :class="[
                                    formErrors.participant_phone
                                        ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500'
                                        : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'rounded-xs bg-tg-bg text-tg-text-strong placeholder:text-tg-text-muted/60 w-full px-3.5 py-2.5 text-sm outline-none transition-colors focus:ring-1 sm:text-base',
                                ]"
                                placeholder="+44 7123 456789"
                            />
                            <p
                                v-if="formErrors.participant_phone"
                                class="mt-1 text-xs font-medium text-rose-400"
                            >
                                {{ formErrors.participant_phone }}
                            </p>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label
                                class="text-tg-text-muted mb-1 block text-xs font-semibold uppercase tracking-wider"
                            >
                                Player Age / Special Notes
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                :class="[
                                    formErrors.notes
                                        ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500'
                                        : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'rounded-xs bg-tg-bg text-tg-text-strong placeholder:text-tg-text-muted/60 w-full resize-y px-3.5 py-2.5 text-sm outline-none transition-colors focus:ring-1 sm:text-base',
                                ]"
                                placeholder="Player age or medical notes..."
                            />
                            <p
                                v-if="formErrors.notes"
                                class="mt-1 text-xs font-medium text-rose-400"
                            >
                                {{ formErrors.notes }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="processing || schedules.length === 0"
                            class="tg-btn w-full cursor-pointer justify-center py-3 text-xs disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <span>{{
                                processing
                                    ? 'Processing...'
                                    : activity.slug.includes('trial')
                                      ? 'Book a Trial'
                                      : 'Confirm Booking'
                            }}</span>
                        </button>

                        <div
                            class="text-tg-text-muted flex items-center justify-center pt-1 text-center text-[11px]"
                        >
                            <span>Try a session. See how they develop.</span>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>
