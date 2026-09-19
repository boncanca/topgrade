<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import {
    ArrowLeft,
    Clock,
    MapPin,
    Users,
    CheckCircle2,
    AlertCircle
} from '@lucide/vue';

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
    schedule_id: props.schedules.length > 0 ? props.schedules[0].id.toString() : '',
    participant_name: '',
    participant_email: '',
    participant_phone: '',
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || 'Europe/London',
    notes: '',
});

watch(
    () => props.schedules,
    (schedules: Schedule[]) => {
        if (schedules && schedules.length > 0 && !form.value.schedule_id) {
            form.value.schedule_id = schedules[0].id.toString();
        }
    },
    { immediate: true }
);

function selectSchedule(id: number | string) {
    form.value.schedule_id = id.toString();
}

const processing = ref(false);
const submitted = ref(false);

const priceFormatted = computed(() => {
    if (props.activity.price === '0' || props.activity.price === '0.00') {
        return 'FREE';
    }
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: props.activity.currency || 'GBP',
    }).format(parseFloat(props.activity.price));
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

function getSelectedSchedule(): Schedule | undefined {
    return props.schedules.find(s => s.id.toString() === form.value.schedule_id);
}
</script>

<template>
    <Head :title="`${activity.name} - TopGrade London FC`" />

    <div class="min-h-screen bg-tg-bg text-tg-text">
        <!-- Hero Section with Background Photography -->
        <section class="relative min-h-[36vh] sm:min-h-[40vh] flex items-end overflow-hidden pb-12 pt-24 sm:pt-28 border-b border-tg-border">
            <!-- Background Image with Solid Dark Contrast -->
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="background-image: url('/images/club/training_pitch_evening.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-tg-bg via-tg-bg/85 to-transparent" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-4">
                <!-- Navigation Link -->
                <Link
                    href="/bookings"
                    class="tg-btn ghost inline-flex items-center gap-2 text-xs py-1.5 px-3"
                >
                    <ArrowLeft class="w-3.5 h-3.5" />
                    <span>Back to Bookings</span>
                </Link>

                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="px-2.5 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-text-muted text-xs font-semibold">
                        {{ activity.duration_minutes }} Minutes
                    </span>
                    <span class="px-2.5 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-text-muted text-xs font-semibold flex items-center gap-1">
                        <Users class="w-3.5 h-3.5 text-tg-accent" />
                        <span>Max {{ activity.capacity }} Players</span>
                    </span>
                    <span class="px-3 py-1 rounded-xs bg-tg-accent text-tg-text-strong text-xs font-bold uppercase tracking-wider">
                        {{ priceFormatted }}
                    </span>
                </div>

                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    {{ activity.name }}
                </h1>
            </div>
        </section>

        <!-- Main Content Area -->
        <section class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid gap-10 lg:grid-cols-3 items-start">
                <!-- Activity Information & Overview -->
                <div class="lg:col-span-2 space-y-10">
                    <!-- Quick Stats Grid -->
                    <div class="grid grid-cols-3 gap-4 p-6 rounded-sm bg-tg-bg-deep/80 border border-tg-border">
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-xs text-tg-accent font-semibold uppercase tracking-wider">
                                <Clock class="w-3.5 h-3.5" />
                                <span>Duration</span>
                            </div>
                            <div class="text-lg font-bold text-tg-text-strong">{{ activity.duration_minutes }} mins</div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-xs text-tg-accent font-semibold uppercase tracking-wider">
                                <MapPin class="w-3.5 h-3.5" />
                                <span>Location</span>
                            </div>
                            <div class="text-sm font-bold text-tg-text-strong truncate">{{ activity.location }}</div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-xs text-tg-accent font-semibold uppercase tracking-wider">
                                <Users class="w-3.5 h-3.5" />
                                <span>Group Size</span>
                            </div>
                            <div class="text-lg font-bold text-tg-text-strong">{{ activity.capacity }} Max</div>
                        </div>
                    </div>

                    <!-- Program Overview -->
                    <div class="space-y-3">
                        <span class="text-xs font-bold uppercase tracking-widest text-tg-accent block">Details</span>
                        <h2
                            class="text-2xl font-normal uppercase text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            Programme Overview
                        </h2>
                        <p class="text-tg-text text-sm sm:text-base leading-relaxed">
                            {{ activity.description }}
                        </p>
                    </div>

                    <!-- Included Features -->
                    <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-6">
                        <h3
                            class="text-xl font-normal uppercase text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            What's Included in This Session
                        </h3>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="flex items-start gap-3 text-tg-text text-sm">
                                <CheckCircle2 class="w-4 h-4 text-tg-accent shrink-0 mt-0.5" />
                                <span>Dedicated club coaches and training staff</span>
                            </div>
                            <div class="flex items-start gap-3 text-tg-text text-sm">
                                <CheckCircle2 class="w-4 h-4 text-tg-accent shrink-0 mt-0.5" />
                                <span>Technical ball mastery & tactical match play</span>
                            </div>
                            <div class="flex items-start gap-3 text-tg-text text-sm">
                                <CheckCircle2 class="w-4 h-4 text-tg-accent shrink-0 mt-0.5" />
                                <span>Quality pitch and training equipment setup</span>
                            </div>
                            <div class="flex items-start gap-3 text-tg-text text-sm">
                                <CheckCircle2 class="w-4 h-4 text-tg-accent shrink-0 mt-0.5" />
                                <span>Post-session feedback & player evaluation</span>
                            </div>
                        </div>
                    </div>

                    <!-- Venue Details -->
                    <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-3">
                        <div class="flex items-center gap-2">
                            <MapPin class="w-4 h-4 text-tg-accent" />
                            <h3
                                class="text-xl font-normal uppercase text-tg-text-strong"
                                style="font-family: var(--tg-display);"
                            >
                                Venue & Facility
                            </h3>
                        </div>
                        <p class="text-tg-text-muted text-sm leading-relaxed">
                            Sessions take place on our high-quality pitches at <strong class="text-tg-text-strong">{{ activity.location }}</strong>. Changing rooms, parking, and parent viewing areas are available on site.
                        </p>
                    </div>
                </div>

                <!-- Sticky Booking Sidebar Form -->
                <div class="sticky top-24 rounded-sm bg-tg-bg-deep border border-tg-border p-6 sm:p-8 space-y-6 shadow-xl">
                    <div class="flex items-baseline justify-between border-b border-tg-border pb-5">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-tg-text-muted">Session Fee</span>
                            <div class="text-3xl font-bold text-tg-text-strong mt-0.5">{{ priceFormatted }}</div>
                        </div>
                        <span class="text-xs text-tg-text-muted">per participant</span>
                    </div>

                    <!-- Success State -->
                    <div v-if="submitted" class="p-6 rounded-sm bg-tg-bg border border-tg-accent text-center space-y-3">
                        <CheckCircle2 class="w-12 h-12 text-tg-accent mx-auto" />
                        <h4
                            class="text-xl font-normal uppercase text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            Booking Confirmed!
                        </h4>
                        <p class="text-xs text-tg-text-muted">
                            We have received your registration. Check your email for session details and pitch directions.
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
                        <!-- Choose Schedule Slot -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-2.5">
                                Select Session Date & Time <span class="text-tg-accent">*</span>
                            </label>

                            <div v-if="schedules.length === 0" class="p-4 rounded-sm bg-tg-bg border border-tg-border text-tg-highlight text-xs flex items-center gap-2">
                                <AlertCircle class="w-4 h-4 shrink-0 text-tg-highlight" />
                                <span>No upcoming slots available currently. Check back soon or contact us.</span>
                            </div>

                            <div v-else class="space-y-2 max-h-52 overflow-y-auto pr-1">
                                <label
                                    v-for="schedule in schedules"
                                    :key="schedule.id"
                                    @click="selectSchedule(schedule.id)"
                                    :class="[
                                        String(form.schedule_id) === String(schedule.id)
                                            ? 'border-tg-accent bg-tg-accent/15 ring-1 ring-tg-accent'
                                            : 'border-tg-border bg-tg-bg/70 hover:border-tg-border-strong',
                                        'flex items-center justify-between p-3 rounded-xs border cursor-pointer transition-all'
                                    ]"
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            v-model="form.schedule_id"
                                            :value="schedule.id.toString()"
                                            type="radio"
                                            name="schedule"
                                            class="accent-pink-500 w-4 h-4"
                                            required
                                        />
                                        <div>
                                            <div class="text-xs font-bold text-tg-text-strong">
                                                {{ formatScheduleDate(schedule.starts_at) }}
                                            </div>
                                            <div class="text-[11px] text-tg-text-muted mt-0.5">
                                                {{ formatScheduleTime(schedule.starts_at, schedule.ends_at) }}
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        :class="[
                                            schedule.available_spots > 0 ? 'text-tg-accent bg-tg-bg border-tg-accent/30' : 'text-rose-400 bg-tg-bg border-rose-500/30',
                                            'px-2 py-0.5 rounded-xs text-[10px] font-semibold border'
                                        ]"
                                    >
                                        {{ schedule.available_spots }} left
                                    </span>
                                </label>
                            </div>
                            <p v-if="formErrors.schedule_id" class="mt-1 text-xs text-rose-400">
                                {{ formErrors.schedule_id }}
                            </p>
                        </div>

                        <!-- Participant Name -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1">
                                Participant / Parent Name <span class="text-tg-accent">*</span>
                            </label>
                            <input
                                v-model="form.participant_name"
                                type="text"
                                required
                                class="w-full rounded-xs border border-tg-border bg-tg-bg px-3.5 py-2.5 text-sm text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:border-tg-accent focus:ring-1 focus:ring-tg-accent transition-colors"
                                placeholder="e.g. Alex Smith"
                            />
                            <p v-if="formErrors.participant_name" class="mt-1 text-xs text-rose-400">
                                {{ formErrors.participant_name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1">
                                Email Address <span class="text-tg-accent">*</span>
                            </label>
                            <input
                                v-model="form.participant_email"
                                type="email"
                                required
                                class="w-full rounded-xs border border-tg-border bg-tg-bg px-3.5 py-2.5 text-sm text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:border-tg-accent focus:ring-1 focus:ring-tg-accent transition-colors"
                                placeholder="alex@example.com"
                            />
                            <p v-if="formErrors.participant_email" class="mt-1 text-xs text-rose-400">
                                {{ formErrors.participant_email }}
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1">
                                Phone Number (optional)
                            </label>
                            <input
                                v-model="form.participant_phone"
                                type="tel"
                                class="w-full rounded-xs border border-tg-border bg-tg-bg px-3.5 py-2.5 text-sm text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:border-tg-accent focus:ring-1 focus:ring-tg-accent transition-colors"
                                placeholder="+44 7123 456789"
                            />
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1">
                                Player Age / Special Notes
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="2"
                                class="w-full rounded-xs border border-tg-border bg-tg-bg px-3.5 py-2.5 text-sm text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:border-tg-accent focus:ring-1 focus:ring-tg-accent transition-colors"
                                placeholder="Player age or medical notes..."
                            />
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="processing || schedules.length === 0"
                            class="tg-btn w-full justify-center py-3 text-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                        >
                            <span>{{ processing ? 'Processing...' : (activity.slug.includes('trial') ? 'Book a Trial' : 'Confirm Booking') }}</span>
                        </button>

                        <div class="flex items-center justify-center text-[11px] text-tg-text-muted pt-1 text-center">
                            <span>Try a session. See how they develop.</span>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>


