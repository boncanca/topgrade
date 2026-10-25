<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import {
    CheckCircle2,
    ChevronRight,
    Image as ImageIcon,
    Send,
    Shield,
    Sparkles,
    Users,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

interface Player {
    name: string;
    number?: number | string;
    position?: string;
    role?: string;
}

interface MomentPhoto {
    id: number;
    url: string;
    name: string;
}

interface TeamMoment {
    id: number;
    title: string;
    slug: string;
    description?: string;
    cover_url?: string;
    photos?: MomentPhoto[];
}

interface TeamItem {
    id: number;
    name: string;
    slug: string;
    age_group: string;
    stage: string;
    description: string;
    visual_variant: string;
    image_url: string;
    players: Player[];
    players_count: number;
    moment: TeamMoment | null;
}

interface PublishedMoment {
    id: number;
    title: string;
    slug: string;
    description: string;
    cover_url: string;
    images_count: number;
}

const props = defineProps<{
    teams: TeamItem[];
    moments: PublishedMoment[];
}>();

const selectedAgeGroup = ref<string>('all');
const trialFormRef = ref<HTMLElement | null>(null);

const filteredTeams = computed(() => {
    if (selectedAgeGroup.value === 'all') {
        return props.teams;
    }

    return props.teams.filter(
        (team) =>
            team.age_group.toLowerCase().includes(selectedAgeGroup.value.toLowerCase()) ||
            team.slug.toLowerCase().includes(selectedAgeGroup.value.toLowerCase())
    );
});

const form = useForm({
    parent_name: '',
    email: '',
    phone: '',
    player_name: '',
    player_dob: '',
    age_group: 'U9–U10',
    preferred_position: '',
    experience: '',
    notes: '',
});

const submitted = ref(false);

function applyForTeam(ageGroup: string) {
    form.age_group = ageGroup;

    if (trialFormRef.value) {
        trialFormRef.value.scrollIntoView({ behavior: 'smooth' });
    }
}

function submitTrialApplication() {
    form.post('/teams/apply', {
        preserveScroll: true,
        onSuccess: () => {
            submitted.value = true;
            form.reset('player_name', 'player_dob', 'preferred_position', 'experience', 'notes');
        },
    });
}
</script>

<template>
    <SeoHead
        title="Youth Teams & Squads – TopGrade London FC"
        description="Explore TopGrade London FC youth squads from U7 through U16. View team rosters, training galleries, and register for upcoming trials."
    />

    <main class="min-h-screen bg-neutral-950 text-neutral-100 selection:bg-amber-400 selection:text-neutral-950">
        <!-- Hero Section -->
        <section class="relative overflow-hidden border-b border-neutral-800/80 bg-neutral-950 px-4 py-20 sm:px-6 lg:px-8">
            <!-- Background Glow -->
            <div class="pointer-events-none absolute -left-40 -top-40 h-96 w-96 rounded-full bg-amber-500/10 blur-[120px]" />
            <div class="pointer-events-none absolute -right-40 top-20 h-96 w-96 rounded-full bg-emerald-500/10 blur-[120px]" />

            <div class="mx-auto max-w-6xl text-center">
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-amber-400">
                    <Shield class="h-3.5 w-3.5" />
                    <span>TopGrade London FC Squads</span>
                </div>

                <h1 class="mt-6 text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                    Youth Teams &
                    <span class="bg-gradient-to-r from-amber-400 via-amber-200 to-amber-500 bg-clip-text text-transparent">
                        Player Squads
                    </span>
                </h1>

                <p class="mx-auto mt-6 max-w-2xl text-base text-neutral-400 sm:text-lg">
                    From our U7 Foundation Phase through U16 Junior Youth Competition, TopGrade London FC squads combine high-tempo technical mastery, character building, and structured matchday fixtures across London.
                </p>

                <!-- Age Group Filter Bar -->
                <div class="mt-10 flex flex-wrap items-center justify-center gap-2">
                    <button
                        type="button"
                        :class="[
                            selectedAgeGroup === 'all'
                                ? 'bg-amber-400 text-neutral-950 font-semibold shadow-lg shadow-amber-400/20'
                                : 'bg-neutral-900 text-neutral-400 hover:bg-neutral-800 hover:text-neutral-200 border border-neutral-800',
                            'rounded-full px-5 py-2 text-xs font-medium transition-all duration-200',
                        ]"
                        @click="selectedAgeGroup = 'all'"
                    >
                        All Squads ({{ teams.length }})
                    </button>
                    <button
                        v-for="team in teams"
                        :key="team.id"
                        type="button"
                        :class="[
                            selectedAgeGroup === team.slug
                                ? 'bg-amber-400 text-neutral-950 font-semibold shadow-lg shadow-amber-400/20'
                                : 'bg-neutral-900 text-neutral-400 hover:bg-neutral-800 hover:text-neutral-200 border border-neutral-800',
                            'rounded-full px-5 py-2 text-xs font-medium transition-all duration-200',
                        ]"
                        @click="selectedAgeGroup = team.slug"
                    >
                        {{ team.age_group }}
                    </button>
                </div>
            </div>
        </section>

        <!-- Squad Cards Showcase -->
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="space-y-16">
                <article
                    v-for="team in filteredTeams"
                    :key="team.id"
                    :id="team.slug"
                    class="group relative overflow-hidden rounded-3xl border border-neutral-800/80 bg-neutral-900/60 p-6 backdrop-blur-sm transition-all duration-300 hover:border-neutral-700 sm:p-8 lg:p-10"
                >
                    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
                        <!-- Squad Photo & Highlights -->
                        <div class="lg:col-span-5">
                            <div class="relative aspect-[4/3] overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-950 shadow-2xl">
                                <img
                                    :src="team.image_url"
                                    :alt="`${team.name} Squad Photo`"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/80 via-transparent to-transparent" />

                                <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between">
                                    <span class="rounded-lg bg-neutral-950/90 px-3 py-1 font-mono text-xs font-bold text-amber-400 backdrop-blur-sm border border-neutral-800">
                                        {{ team.age_group }}
                                    </span>
                                    <span class="rounded-lg bg-neutral-950/90 px-3 py-1 text-xs font-medium text-neutral-300 backdrop-blur-sm border border-neutral-800">
                                        {{ team.stage }}
                                    </span>
                                </div>
                            </div>

                            <!-- Linked Gallery Moments Preview -->
                            <div v-if="team.moment" class="mt-4 rounded-xl border border-neutral-800/80 bg-neutral-950/60 p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-xs font-semibold text-neutral-300">
                                        <ImageIcon class="h-3.5 w-3.5 text-amber-400" />
                                        <span>Linked Gallery: {{ team.moment.title }}</span>
                                    </div>
                                    <Link
                                        :href="`/moments/${team.moment.slug}`"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-amber-400 hover:underline"
                                    >
                                        <span>View Album</span>
                                        <ChevronRight class="h-3 w-3" />
                                    </Link>
                                </div>

                                <div v-if="team.moment.photos && team.moment.photos.length > 0" class="mt-3 grid grid-cols-3 gap-2">
                                    <div
                                        v-for="photo in team.moment.photos.slice(0, 3)"
                                        :key="photo.id"
                                        class="aspect-square overflow-hidden rounded-lg border border-neutral-800 bg-neutral-900"
                                    >
                                        <img
                                            :src="photo.url"
                                            :alt="photo.name || 'Team moment photo'"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex items-center gap-3">
                                <button
                                    type="button"
                                    class="flex-1 rounded-xl bg-amber-400 px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-neutral-950 transition-all hover:bg-amber-300 shadow-lg shadow-amber-400/10"
                                    @click="applyForTeam(team.age_group)"
                                >
                                    Apply For {{ team.age_group }} Trial
                                </button>
                                <Link
                                    v-if="team.moment"
                                    :href="`/moments/${team.moment.slug}`"
                                    class="rounded-xl border border-neutral-700 bg-neutral-800/80 px-4 py-3 text-xs font-semibold text-neutral-200 transition-colors hover:bg-neutral-700"
                                >
                                    Gallery
                                </Link>
                            </div>
                        </div>

                        <!-- Squad Details & Player Roster -->
                        <div class="flex flex-col justify-between lg:col-span-7">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-amber-400/10 px-3 py-1 font-mono text-xs font-semibold text-amber-400 border border-amber-400/20">
                                        {{ team.age_group }} SQUAD
                                    </span>
                                    <span class="rounded-full bg-neutral-800 px-3 py-1 text-xs text-neutral-400">
                                        {{ team.stage }}
                                    </span>
                                    <span v-if="team.players_count > 0" class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-400 border border-emerald-500/20">
                                        {{ team.players_count }} Registered Players
                                    </span>
                                </div>

                                <h2 class="mt-4 text-2xl font-bold text-white sm:text-3xl">
                                    {{ team.name }}
                                </h2>

                                <p class="mt-3 text-sm leading-relaxed text-neutral-300">
                                    {{ team.description }}
                                </p>

                                <!-- Players Roster -->
                                <div class="mt-8">
                                    <div class="flex items-center justify-between border-b border-neutral-800 pb-3">
                                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-neutral-400">
                                            <Users class="h-4 w-4 text-amber-400" />
                                            <span>Current Squad Roster</span>
                                        </div>
                                        <span class="text-xs text-neutral-500">
                                            Matchday & Academy Squad
                                        </span>
                                    </div>

                                    <div v-if="team.players && team.players.length > 0" class="mt-4 divide-y divide-neutral-800/60">
                                        <div
                                            v-for="(player, pIdx) in team.players"
                                            :key="pIdx"
                                            class="flex items-center justify-between py-2.5 transition-colors hover:bg-neutral-800/30 px-2 rounded-lg"
                                        >
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-neutral-800 font-mono text-xs font-bold text-amber-400">
                                                    {{ player.number ? `#${player.number}` : (pIdx + 1) }}
                                                </span>
                                                <span class="text-sm font-semibold text-neutral-100">
                                                    {{ player.name }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-neutral-400 font-medium">
                                                    {{ player.position || 'Squad Player' }}
                                                </span>
                                                <span
                                                    v-if="player.role && player.role.toLowerCase().includes('captain')"
                                                    class="rounded-full bg-amber-400/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-300 border border-amber-400/30"
                                                >
                                                    {{ player.role }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Empty players state -->
                                    <div v-else class="mt-4 rounded-xl border border-dashed border-neutral-800 p-6 text-center text-xs text-neutral-500">
                                        Squad roster currently being updated for upcoming league fixtures.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <!-- "Applies" / Join a Team & Trial Request Form -->
        <section ref="trialFormRef" id="apply" class="relative border-t border-neutral-800/80 bg-neutral-900/40 px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl">
                <div class="rounded-3xl border border-neutral-800 bg-neutral-950 p-8 sm:p-12 shadow-2xl relative overflow-hidden">
                    <div class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rounded-full bg-amber-500/10 blur-[90px]" />

                    <div class="text-center max-w-2xl mx-auto">
                        <div class="inline-flex items-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-amber-400">
                            <Sparkles class="h-3.5 w-3.5" />
                            <span>Trial Applications & Registration</span>
                        </div>

                        <h2 class="mt-4 text-3xl font-extrabold text-white sm:text-4xl">
                            Apply To Join TopGrade London FC
                        </h2>
                        <p class="mt-3 text-sm text-neutral-400 sm:text-base">
                            Complete the application form below to request a trial session with one of our youth squads. Our FA-qualified coaching staff will review and get in touch with session dates.
                        </p>
                    </div>

                    <!-- Success Alert -->
                    <div
                        v-if="submitted"
                        class="mt-8 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-6 text-center"
                    >
                        <CheckCircle2 class="mx-auto h-10 w-10 text-emerald-400" />
                        <h3 class="mt-3 text-lg font-bold text-emerald-300">Application Received!</h3>
                        <p class="mt-1 text-sm text-neutral-300">
                            Thank you! We have received your trial application for {{ form.age_group }}. A coaching staff member will contact you via email or phone shortly.
                        </p>
                        <button
                            type="button"
                            class="mt-4 text-xs font-semibold text-amber-400 underline"
                            @click="submitted = false"
                        >
                            Submit another application
                        </button>
                    </div>

                    <!-- Form -->
                    <form v-else class="mt-10 space-y-6" @submit.prevent="submitTrialApplication">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Parent Name -->
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-neutral-300">
                                    Parent / Guardian Full Name *
                                </label>
                                <input
                                    v-model="form.parent_name"
                                    type="text"
                                    required
                                    placeholder="e.g. Sarah Jenkins"
                                    class="mt-2 w-full rounded-xl border border-neutral-800 bg-neutral-900 px-4 py-3 text-sm text-white placeholder-neutral-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                                />
                                <span v-if="form.errors.parent_name" class="mt-1 block text-xs text-rose-400">
                                    {{ form.errors.parent_name }}
                                </span>
                            </div>

                            <!-- Contact Email -->
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-neutral-300">
                                    Contact Email *
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="parent@example.com"
                                    class="mt-2 w-full rounded-xl border border-neutral-800 bg-neutral-900 px-4 py-3 text-sm text-white placeholder-neutral-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                                />
                                <span v-if="form.errors.email" class="mt-1 block text-xs text-rose-400">
                                    {{ form.errors.email }}
                                </span>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-neutral-300">
                                    Contact Phone Number *
                                </label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    required
                                    placeholder="07123 456789"
                                    class="mt-2 w-full rounded-xl border border-neutral-800 bg-neutral-900 px-4 py-3 text-sm text-white placeholder-neutral-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                                />
                                <span v-if="form.errors.phone" class="mt-1 block text-xs text-rose-400">
                                    {{ form.errors.phone }}
                                </span>
                            </div>

                            <!-- Target Age Group -->
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-neutral-300">
                                    Target Age Group *
                                </label>
                                <select
                                    v-model="form.age_group"
                                    required
                                    class="mt-2 w-full rounded-xl border border-neutral-800 bg-neutral-900 px-4 py-3 text-sm text-white focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                                >
                                    <option v-for="team in teams" :key="team.id" :value="team.age_group">
                                        {{ team.name }} ({{ team.age_group }}) – {{ team.stage }}
                                    </option>
                                </select>
                                <span v-if="form.errors.age_group" class="mt-1 block text-xs text-rose-400">
                                    {{ form.errors.age_group }}
                                </span>
                            </div>

                            <!-- Player Full Name -->
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-neutral-300">
                                    Player Full Name *
                                </label>
                                <input
                                    v-model="form.player_name"
                                    type="text"
                                    required
                                    placeholder="e.g. Leo Jenkins"
                                    class="mt-2 w-full rounded-xl border border-neutral-800 bg-neutral-900 px-4 py-3 text-sm text-white placeholder-neutral-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                                />
                                <span v-if="form.errors.player_name" class="mt-1 block text-xs text-rose-400">
                                    {{ form.errors.player_name }}
                                </span>
                            </div>

                            <!-- Player DOB -->
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-neutral-300">
                                    Player Date of Birth *
                                </label>
                                <input
                                    v-model="form.player_dob"
                                    type="date"
                                    required
                                    class="mt-2 w-full rounded-xl border border-neutral-800 bg-neutral-900 px-4 py-3 text-sm text-white placeholder-neutral-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                                />
                                <span v-if="form.errors.player_dob" class="mt-1 block text-xs text-rose-400">
                                    {{ form.errors.player_dob }}
                                </span>
                            </div>

                            <!-- Preferred Position -->
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-neutral-300">
                                    Preferred Playing Position
                                </label>
                                <input
                                    v-model="form.preferred_position"
                                    type="text"
                                    placeholder="e.g. Midfielder, Goalkeeper, Striker"
                                    class="mt-2 w-full rounded-xl border border-neutral-800 bg-neutral-900 px-4 py-3 text-sm text-white placeholder-neutral-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                                />
                            </div>

                            <!-- Experience / Current Club -->
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-neutral-300">
                                    Current Club / Playing Experience
                                </label>
                                <input
                                    v-model="form.experience"
                                    type="text"
                                    placeholder="e.g. School team, Sunday league, Grassroots"
                                    class="mt-2 w-full rounded-xl border border-neutral-800 bg-neutral-900 px-4 py-3 text-sm text-white placeholder-neutral-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                                />
                            </div>
                        </div>

                        <!-- Notes / Medical Info -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-neutral-300">
                                Additional Notes or Medical Considerations
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                placeholder="Any allergies, previous injuries, or details you would like the coaching team to know..."
                                class="mt-2 w-full rounded-xl border border-neutral-800 bg-neutral-900 px-4 py-3 text-sm text-white placeholder-neutral-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                            />
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-400 px-6 py-4 text-sm font-bold uppercase tracking-wider text-neutral-950 transition-all hover:bg-amber-300 disabled:opacity-50 shadow-xl shadow-amber-400/20"
                            >
                                <Send class="h-4 w-4" />
                                <span>{{ form.processing ? 'Submitting Application...' : 'Submit Trial Application' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</template>
