<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import {
    ArrowLeft,
    Clock,
    MapPin,
    Users,
    CheckCircle2,
    Shield,
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
    (schedules) => {
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

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Hero Section with Background Image -->
        <section class="relative min-h-[40vh] flex items-end overflow-hidden pb-12 pt-28">
            <!-- Background Image with Overlay -->
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30"
                style="background-image: url('/images/hero_action.png')"
            />
            <div class="absolute inset-0 bg-slate-950/80" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-6">
                <!-- Navigation Link -->
                <Link
                    href="/bookings"
                    class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-slate-300 hover:text-white transition-colors bg-slate-900 px-3.5 py-1.5 rounded-full border border-slate-800"
                >
                    <ArrowLeft class="w-4 h-4" />
                    <span>Back to Training Programmes</span>
                </Link>

                <div class="flex flex-wrap items-center gap-3">
                    <span class="px-3 py-1 rounded-md bg-slate-800 border border-slate-700 text-slate-300 text-xs font-semibold">
                        {{ activity.duration_minutes }} Minutes
                    </span>
                    <span class="px-3 py-1 rounded-md bg-slate-800 border border-slate-700 text-slate-300 text-xs font-semibold flex items-center gap-1">
                        <Users class="w-3.5 h-3.5" />
                        <span>Max {{ activity.capacity }} Players</span>
                    </span>
                    <span class="px-3 py-1 rounded-md bg-purple-600 text-white text-xs font-bold">
                        {{ priceFormatted }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                    {{ activity.name }}
                </h1>
            </div>
        </section>

        <!-- Main Content Area -->
        <section class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid gap-12 lg:grid-cols-3 items-start">
                <!-- Activity Information & Overview -->
                <div class="lg:col-span-2 space-y-10">
                    <!-- Quick Stats Grid -->
                    <div class="grid grid-cols-3 gap-4 p-6 rounded-xl bg-slate-900 border border-slate-800">
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-xs text-slate-400 font-semibold uppercase tracking-wider">
                                <Clock class="w-4 h-4" />
                                <span>Duration</span>
                            </div>
                            <div class="text-lg font-bold text-white">{{ activity.duration_minutes }} mins</div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-xs text-slate-400 font-semibold uppercase tracking-wider">
                                <MapPin class="w-4 h-4" />
                                <span>Location</span>
                            </div>
                            <div class="text-sm font-bold text-white truncate">{{ activity.location }}</div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-xs text-slate-400 font-semibold uppercase tracking-wider">
                                <Users class="w-4 h-4" />
                                <span>Group Size</span>
                            </div>
                            <div class="text-lg font-bold text-white">{{ activity.capacity }} Max</div>
                        </div>
                    </div>

                    <!-- Program Overview -->
                    <div class="space-y-4">
                        <h2 class="text-2xl font-bold text-white">Programme Overview</h2>
                        <p class="text-slate-300 text-base leading-relaxed">
                            {{ activity.description }}
                        </p>
                    </div>

                    <!-- Included Features -->
                    <div class="p-8 rounded-xl bg-slate-900 border border-slate-800 space-y-6">
                        <h3 class="text-xl font-bold text-white">
                            What's Included in This Session
                        </h3>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="flex items-start gap-3 text-slate-300 text-sm">
                                <CheckCircle2 class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
                                <span>UEFA-licensed lead coaches & academy assistants</span>
                            </div>
                            <div class="flex items-start gap-3 text-slate-300 text-sm">
                                <CheckCircle2 class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
                                <span>Full technical ball mastery & tactical match play</span>
                            </div>
                            <div class="flex items-start gap-3 text-slate-300 text-sm">
                                <CheckCircle2 class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
                                <span>High-performance pitch and equipment setup</span>
                            </div>
                            <div class="flex items-start gap-3 text-slate-300 text-sm">
                                <CheckCircle2 class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
                                <span>Post-session player feedback & academy trial recommendation</span>
                            </div>
                        </div>
                    </div>

                    <!-- Venue Details -->
                    <div class="p-8 rounded-xl bg-slate-900 border border-slate-800 space-y-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <MapPin class="w-5 h-5 text-purple-400" />
                            <span>Venue & Facility</span>
                        </h3>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            Sessions take place on our high-quality pitches at <strong class="text-white">{{ activity.location }}</strong>. Changing rooms, parking, and parent viewing areas are available on site.
                        </p>
                    </div>
                </div>

                <!-- Sticky Booking Sidebar Form -->
                <div class="sticky top-24 rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-lg">
                    <div class="flex items-baseline justify-between border-b border-slate-800 pb-6">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Session Fee</span>
                            <div class="text-3xl font-extrabold text-white mt-1">{{ priceFormatted }}</div>
                        </div>
                        <span class="text-xs text-slate-400">per participant</span>
                    </div>

                    <!-- Success State -->
                    <div v-if="submitted" class="p-6 rounded-xl bg-slate-950 border border-emerald-500/40 text-emerald-200 text-center space-y-3">
                        <CheckCircle2 class="w-12 h-12 text-emerald-400 mx-auto" />
                        <h4 class="text-xl font-bold text-white">Booking Confirmed!</h4>
                        <p class="text-xs text-slate-300">
                            We have received your registration. Check your email for session details and pitch directions.
                        </p>
                        <Button
                            type="button"
                            variant="outline"
                            class="mt-4 border-slate-700 text-white hover:bg-slate-800"
                            @click="submitted = false"
                        >
                            Book Another Session
                        </Button>
                    </div>

                    <!-- Booking Form -->
                    <form v-else class="space-y-5" @submit.prevent="submit">
                        <!-- Choose Schedule Slot -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 dark:text-slate-400 mb-3">
                                Select Session Date & Time <span class="text-destructive">*</span>
                            </label>

                            <div v-if="schedules.length === 0" class="p-4 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs flex items-center gap-2">
                                <AlertCircle class="w-4 h-4 shrink-0" />
                                <span>No upcoming slots available currently. Check back soon or contact us.</span>
                            </div>

                            <div v-else class="space-y-2 max-h-52 overflow-y-auto pr-1">
                                <label
                                    v-for="schedule in schedules"
                                    :key="schedule.id"
                                    @click="selectSchedule(schedule.id)"
                                    :class="[
                                        String(form.schedule_id) === String(schedule.id)
                                            ? 'border-purple-500 bg-purple-950/40 dark:bg-purple-900/40 ring-1 ring-purple-500'
                                            : 'border-slate-800 bg-slate-950 dark:bg-slate-900 hover:border-slate-700',
                                        'flex items-center justify-between p-3.5 rounded-lg border cursor-pointer transition-all'
                                    ]"
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            v-model="form.schedule_id"
                                            :value="schedule.id.toString()"
                                            type="radio"
                                            name="schedule"
                                            class="accent-purple-500 w-4 h-4"
                                            required
                                        />
                                        <div>
                                            <div class="text-xs font-bold text-white">
                                                {{ formatScheduleDate(schedule.starts_at) }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 mt-0.5">
                                                {{ formatScheduleTime(schedule.starts_at, schedule.ends_at) }}
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        :class="[
                                            schedule.available_spots > 0 ? 'text-emerald-400 bg-slate-900 border-emerald-500/30' : 'text-rose-400 bg-slate-900 border-rose-500/30',
                                            'px-2 py-0.5 rounded text-[10px] font-semibold border'
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
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 dark:text-slate-400 mb-1">
                                Participant / Parent Name <span class="text-destructive">*</span>
                            </label>
                            <input
                                v-model="form.participant_name"
                                type="text"
                                required
                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-purple-500 transition-colors"
                                placeholder="e.g. Alex Smith"
                            />
                            <p v-if="formErrors.participant_name" class="mt-1 text-xs text-rose-400">
                                {{ formErrors.participant_name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 dark:text-slate-400 mb-1">
                                Email Address <span class="text-destructive">*</span>
                            </label>
                            <input
                                v-model="form.participant_email"
                                type="email"
                                required
                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-purple-500 transition-colors"
                                placeholder="alex@example.com"
                            />
                            <p v-if="formErrors.participant_email" class="mt-1 text-xs text-rose-400">
                                {{ formErrors.participant_email }}
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 dark:text-slate-400 mb-1">
                                Phone Number (optional)
                            </label>
                            <input
                                v-model="form.participant_phone"
                                type="tel"
                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-purple-500 transition-colors"
                                placeholder="+44 7123 456789"
                            />
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 dark:text-slate-400 mb-1">
                                Player Age / Special Notes
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="2"
                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-purple-500 transition-colors"
                                placeholder="Player age or medical notes..."
                            />
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="processing || schedules.length === 0"
                            class="w-full py-3.5 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-semibold text-base rounded-lg transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                        >
                            <span>{{ processing ? 'Processing Booking...' : 'Confirm Booking' }}</span>
                        </button>

                        <div class="flex items-center justify-center gap-1.5 text-[11px] text-slate-400 pt-2">
                            <Shield class="w-3.5 h-3.5 text-emerald-400" />
                            <span>Instant confirmation • High safety standards</span>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>


