<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Trophy, Users, Target, MapPin, Award, Maximize2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import ImagePreviewModal from '@/components/Public/ImagePreviewModal.vue';
import type { PreviewImageData } from '@/components/Public/ImagePreviewModal.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

const previewImage = ref<PreviewImageData | null>(null);
const previewModalOpen = ref(false);

function openImagePreview(data: PreviewImageData): void {
    previewImage.value = data;
    previewModalOpen.value = true;
}

const props = defineProps<{
    values?: Array<{ title: string; description: string; icon?: string }>;
    facilities?: Array<{
        name: string;
        subtext: string;
        venue: string;
        surface: string;
        details: string;
    }>;
    staff?: Array<{
        name: string;
        role: string;
        bio: string;
        photo_url?: string | null;
    }>;
}>();

const iconMap: Record<string, any> = {
    Target,
    Award,
    Users,
    Trophy,
};

const defaultValues = [
    {
        title: 'Player-First Development',
        description:
            'Every drill, session plan, and coaching instruction focuses on technical proficiency and long-term player progression.',
        icon: 'Target',
    },
    {
        title: 'Disciplined Coaching Standards',
        description:
            'Led by Head Coach Richard Matey Opoku, our dedicated coaching staff focus on age-appropriate training, disciplined player habits, positive reinforcement, and tactical understanding.',
        icon: 'Award',
    },
    {
        title: 'London Community Roots',
        description:
            'Operating as a non-profit Community Interest Company (CIC) dedicated to structured youth football in North and East London.',
        icon: 'Users',
    },
    {
        title: 'Competitive League Matches',
        description:
            'Sanctioned London youth league participation, tournament showcases, and competitive weekend fixtures.',
        icon: 'Trophy',
    },
];

const values = computed(() => {
    const list =
        props.values && props.values.length > 0 ? props.values : defaultValues;

    return list.map((item) => ({
        ...item,
        iconComponent:
            item.icon && iconMap[item.icon] ? iconMap[item.icon] : Target,
    }));
});

const defaultFacilities = [
    {
        name: 'Frederick Knight Sports Centre',
        subtext: 'Tottenham Powerleague',
        venue: 'Willoughby Lane, Tottenham, London N17 0RT',
        surface: 'All-Weather 3G Floodlit Pitches',
        details:
            'Midweek squad training sessions (Tuesdays & Thursdays) and competitive home match fixtures.',
    },
    {
        name: 'Tottenham Community Sports Centre',
        subtext: 'High Road Sports Facility',
        venue: '701–703 High Road, London N17 8AD',
        surface: 'Sports Centre Training Facility',
        details:
            'Wednesday evening squad training sessions for junior age groups.',
    },
    {
        name: 'Mabley Green Park',
        subtext: 'Homerton, Hackney',
        venue: 'Lee Conservancy Road, London E9 5HW',
        surface: 'Grass & 3G Matchday Pitches',
        details: 'Weekend home league fixtures and squad match play.',
    },
    {
        name: 'Hackney Marshes',
        subtext: 'Homerton Road, Hackney',
        venue: 'Homerton Road, London E9 5PF',
        surface: 'Natural Grass Matchday Pitches',
        details:
            'Saturday youth league matches, tournament fixtures, and inter-club friendlies.',
    },
];

const facilities = computed(() => {
    return props.facilities && props.facilities.length > 0
        ? props.facilities
        : defaultFacilities;
});
</script>

<template>
    <SeoHead
        title="About TopGrade London FC | Youth Football Club"
        description="Learn about TopGrade London FC, our grassroots Community Interest Company (CIC) values, dedicated coaching philosophy, and training facilities in Tottenham and Hackney."
        path="/about"
    />

    <div class="bg-tg-bg text-tg-text min-h-screen">
        <!-- Hero Section: Clean, Solid Sports Presentation -->
        <section
            class="border-tg-border relative flex min-h-[36vh] items-end overflow-hidden border-b pb-12 pt-24 sm:min-h-[42vh] sm:pt-28"
        >
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="
                    background-image: url('/images/club/tactical-coaching-briefing-topgrade-london-fc.webp');
                "
            />
            <div
                class="from-tg-bg via-tg-bg/85 absolute inset-0 bg-gradient-to-t to-transparent"
            />

            <div
                class="relative z-10 mx-auto w-full max-w-5xl space-y-3 px-4 sm:px-6 lg:px-8"
            >
                <span
                    class="rounded-xs bg-tg-bg-deep border-tg-border text-tg-accent inline-block border px-3 py-1 text-xs font-bold uppercase tracking-widest"
                >
                    About the Club
                </span>

                <h1
                    class="text-tg-text-strong max-w-3xl text-4xl font-normal uppercase leading-tight tracking-tight sm:text-6xl"
                    style="font-family: var(--tg-display)"
                >
                    London Youth Football Development
                </h1>
                <p
                    class="text-tg-text max-w-2xl text-base leading-relaxed sm:text-lg"
                >
                    TopGrade London FC is a grassroots Community Interest
                    Company (CIC) delivering structured coaching, teamwork, and
                    competitive playing pathways across Tottenham and Hackney.
                </p>
            </div>
        </section>

        <!-- Club Environment -->
        <section
            class="bg-tg-bg-deep/60 border-tg-border border-b px-4 py-20 sm:px-6 lg:px-8"
        >
            <div
                class="mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-2"
            >
                <div class="space-y-5">
                    <span
                        class="text-tg-accent block text-xs font-bold uppercase tracking-widest"
                    >
                        Club Environment
                    </span>
                    <h2
                        class="text-tg-text-strong text-3xl font-normal uppercase tracking-tight sm:text-4xl"
                        style="font-family: var(--tg-display)"
                    >
                        Developing Footballers, Building Character
                    </h2>
                    <p class="text-tg-text text-sm leading-relaxed">
                        At TopGrade London FC, youth football is focused on
                        discipline, work ethic, spatial intelligence, and
                        teamwork.
                    </p>
                    <p class="text-tg-text-muted text-sm leading-relaxed">
                        Our squads train weekly under Head Coach Richard Matey
                        Opoku and dedicated coaching staff who emphasize ball
                        control, quick thinking, and competitive bravery.
                        Training takes place at the Frederick Knight Sports
                        Centre and Tottenham Community Sports Centre (TCSC),
                        with home match play at Frederick Knight, Mabley Green
                        Park, and Hackney Marshes.
                    </p>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <Link
                            href="/bookings/free-trial-session"
                            class="tg-btn text-xs"
                        >
                            <span>Book a Trial</span>
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <Link href="/contact" class="tg-btn ghost text-xs">
                            <span>Contact the Club</span>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <div
                        class="border-tg-border bg-tg-bg-deep group relative aspect-[16/10] cursor-pointer overflow-hidden rounded-lg border shadow-sm transition-all duration-300 hover:border-tg-accent/60 sm:aspect-[16/9]"
                        role="button"
                        tabindex="0"
                        aria-label="View full photo of Under-7 and Under-8 squad with medals"
                        @click="
                            openImagePreview({
                                url: '/images/club/u7-u8-squad-photo-topgrade-london-fc.webp',
                                alt: 'TopGrade London FC Under-7 and Under-8 players with medals and coach',
                                title: 'Foundation Phase · U7 – U8 Squad',
                                subtitle:
                                    'Under-7 and Under-8 squad celebrating tournament medals with their coach.',
                            })
                        "
                        @keydown.enter="
                            openImagePreview({
                                url: '/images/club/u7-u8-squad-photo-topgrade-london-fc.webp',
                                alt: 'TopGrade London FC Under-7 and Under-8 players with medals and coach',
                                title: 'Foundation Phase · U7 – U8 Squad',
                                subtitle:
                                    'Under-7 and Under-8 squad celebrating tournament medals with their coach.',
                            })
                        "
                    >
                        <img
                            src="/images/club/u7-u8-squad-photo-topgrade-london-fc.webp"
                            alt="TopGrade London FC Under-7 and Under-8 players with medals and coach"
                            class="h-full w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
                            loading="lazy"
                        />
                        <span
                            class="absolute right-2.5 top-2.5 flex items-center gap-1 rounded-full border border-white/20 bg-black/75 px-2 py-1 text-[10px] text-white opacity-0 backdrop-blur-sm transition-opacity duration-200 group-hover:opacity-100"
                        >
                            <Maximize2 class="text-tg-accent size-3" />
                            <span class="hidden sm:inline">Full Photo</span>
                        </span>
                    </div>
                    <div
                        class="border-tg-border bg-tg-bg-deep group relative aspect-[16/10] cursor-pointer overflow-hidden rounded-lg border shadow-sm transition-all duration-300 hover:border-tg-accent/60 sm:aspect-[16/9]"
                        role="button"
                        tabindex="0"
                        aria-label="View full photo of coach mentoring youth player"
                        @click="
                            openImagePreview({
                                url: '/images/club/coach-mentoring-session-topgrade-london-fc.webp',
                                alt: 'TopGrade coach mentoring a youth football player',
                                title: 'Coaching Mentorship · Individual Focus',
                                subtitle:
                                    'One-on-one pitch briefing, building confidence and game intellect.',
                            })
                        "
                        @keydown.enter="
                            openImagePreview({
                                url: '/images/club/coach-mentoring-session-topgrade-london-fc.webp',
                                alt: 'TopGrade coach mentoring a youth football player',
                                title: 'Coaching Mentorship · Individual Focus',
                                subtitle:
                                    'One-on-one pitch briefing, building confidence and game intellect.',
                            })
                        "
                    >
                        <img
                            src="/images/club/coach-mentoring-session-topgrade-london-fc.webp"
                            alt="TopGrade coach mentoring a youth football player"
                            class="h-full w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
                            loading="lazy"
                        />
                        <span
                            class="absolute right-2.5 top-2.5 flex items-center gap-1 rounded-full border border-white/20 bg-black/75 px-2 py-1 text-[10px] text-white opacity-0 backdrop-blur-sm transition-opacity duration-200 group-hover:opacity-100"
                        >
                            <Maximize2 class="text-tg-accent size-3" />
                            <span class="hidden sm:inline">Full Photo</span>
                        </span>
                    </div>
                    <div
                        class="border-tg-border bg-tg-bg-deep group relative aspect-[16/10] cursor-pointer overflow-hidden rounded-lg border shadow-sm transition-all duration-300 hover:border-tg-accent/60 sm:aspect-[16/9]"
                        role="button"
                        tabindex="0"
                        aria-label="View full photo of evening floodlit training pitch"
                        @click="
                            openImagePreview({
                                url: '/images/club/floodlit-training-pitch-topgrade-london-fc.webp',
                                alt: 'TopGrade evening squad training under floodlights',
                                title: 'Training Facility · All-Weather Floodlit Pitch',
                                subtitle:
                                    'Modern training ground ready for evening squad sessions year-round.',
                            })
                        "
                        @keydown.enter="
                            openImagePreview({
                                url: '/images/club/floodlit-training-pitch-topgrade-london-fc.webp',
                                alt: 'TopGrade evening squad training under floodlights',
                                title: 'Training Facility · All-Weather Floodlit Pitch',
                                subtitle:
                                    'Modern training ground ready for evening squad sessions year-round.',
                            })
                        "
                    >
                        <img
                            src="/images/club/floodlit-training-pitch-topgrade-london-fc.webp"
                            alt="TopGrade evening squad training under floodlights"
                            class="h-full w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
                            loading="lazy"
                        />
                        <span
                            class="absolute right-2.5 top-2.5 flex items-center gap-1 rounded-full border border-white/20 bg-black/75 px-2 py-1 text-[10px] text-white opacity-0 backdrop-blur-sm transition-opacity duration-200 group-hover:opacity-100"
                        >
                            <Maximize2 class="text-tg-accent size-3" />
                            <span class="hidden sm:inline">Full Photo</span>
                        </span>
                    </div>
                    <div
                        class="border-tg-border bg-tg-bg-deep group relative aspect-[16/10] cursor-pointer overflow-hidden rounded-lg border shadow-sm transition-all duration-300 hover:border-tg-accent/60 sm:aspect-[16/9]"
                        role="button"
                        tabindex="0"
                        aria-label="View full photo of youth squad team"
                        @click="
                            openImagePreview({
                                url: '/images/club/youth-squad-team-topgrade-london-fc.webp',
                                alt: 'TopGrade London FC youth squad posing together on the pitch',
                                title: 'Matchday Squad · Team Unity',
                                subtitle:
                                    'Players lined up together on the pitch in official match kit.',
                            })
                        "
                        @keydown.enter="
                            openImagePreview({
                                url: '/images/club/youth-squad-team-topgrade-london-fc.webp',
                                alt: 'TopGrade London FC youth squad posing together on the pitch',
                                title: 'Matchday Squad · Team Unity',
                                subtitle:
                                    'Players lined up together on the pitch in official match kit.',
                            })
                        "
                    >
                        <img
                            src="/images/club/youth-squad-team-topgrade-london-fc.webp"
                            alt="TopGrade London FC youth squad posing together on the pitch"
                            class="h-full w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
                            loading="lazy"
                        />
                        <span
                            class="absolute right-2.5 top-2.5 flex items-center gap-1 rounded-full border border-white/20 bg-black/75 px-2 py-1 text-[10px] text-white opacity-0 backdrop-blur-sm transition-opacity duration-200 group-hover:opacity-100"
                        >
                            <Maximize2 class="text-tg-accent size-3" />
                            <span class="hidden sm:inline">Full Photo</span>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Club Values -->
        <section
            class="bg-tg-bg border-tg-border border-b px-4 py-20 sm:px-6 lg:px-8"
        >
            <div class="mx-auto max-w-7xl">
                <div class="border-tg-border mb-12 max-w-2xl border-b pb-4">
                    <span
                        class="text-tg-accent mb-1 block text-xs font-bold uppercase tracking-widest"
                    >
                        Club Principles
                    </span>
                    <h2
                        class="text-tg-text-strong text-3xl font-normal uppercase tracking-tight sm:text-4xl"
                        style="font-family: var(--tg-display)"
                    >
                        What Defines TopGrade
                    </h2>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="item in values"
                        :key="item.title"
                        class="border-tg-border bg-tg-bg-deep/80 space-y-4 rounded-sm border p-6"
                    >
                        <div
                            class="rounded-xs bg-tg-bg border-tg-border text-tg-accent flex h-10 w-10 items-center justify-center border"
                        >
                            <component
                                :is="item.iconComponent || Target"
                                class="h-5 w-5"
                            />
                        </div>
                        <h3
                            class="text-tg-text-strong text-lg font-normal uppercase tracking-tight"
                            style="font-family: var(--tg-display)"
                        >
                            {{ item.title }}
                        </h3>
                        <p class="text-tg-text-muted text-xs leading-relaxed">
                            {{ item.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Official Grounds & Pitch Locations -->
        <section
            class="bg-tg-bg-deep/60 border-tg-border border-b px-4 py-20 sm:px-6 lg:px-8"
        >
            <div class="mx-auto max-w-7xl">
                <div class="border-tg-border mb-12 max-w-2xl border-b pb-4">
                    <span
                        class="text-tg-accent mb-1 block text-xs font-bold uppercase tracking-widest"
                    >
                        Locations
                    </span>
                    <h2
                        class="text-tg-text-strong text-3xl font-normal uppercase tracking-tight sm:text-4xl"
                        style="font-family: var(--tg-display)"
                    >
                        Where We Train &amp; Play
                    </h2>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div
                        v-for="f in facilities"
                        :key="f.name"
                        class="bg-tg-bg border-tg-border space-y-3 rounded-sm border p-6"
                    >
                        <div>
                            <h3
                                class="text-tg-text-strong text-lg font-normal uppercase tracking-tight"
                                style="font-family: var(--tg-display)"
                            >
                                {{ f.name }}
                            </h3>
                            <span class="text-tg-accent text-xs font-medium">{{
                                f.subtext
                            }}</span>
                        </div>
                        <div
                            class="text-tg-text flex items-start gap-2 text-xs"
                        >
                            <MapPin
                                class="text-tg-accent mt-0.5 h-4 w-4 shrink-0"
                            />
                            <span>{{ f.venue }}</span>
                        </div>
                        <p class="text-tg-text-muted text-xs">
                            <span class="text-tg-text font-bold">Surface:</span>
                            {{ f.surface }}
                        </p>
                        <p
                            class="text-tg-text-muted pt-1 text-xs leading-relaxed"
                        >
                            {{ f.details }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Banner: Solid, Clean -->
        <section
            class="bg-tg-bg-deep border-tg-border relative overflow-hidden border-t px-4 py-24 text-center sm:px-6 lg:px-8"
        >
            <div
                class="absolute inset-0 bg-cover bg-center opacity-25"
                style="background-image: url('/images/club/u7-u8-squad-photo-topgrade-london-fc.webp')"
            />
            <div class="absolute inset-0 bg-black/70" />

            <div class="relative z-10 mx-auto max-w-3xl space-y-5">
                <h2
                    class="text-3xl font-normal uppercase tracking-tight text-white sm:text-4xl"
                    style="font-family: var(--tg-display)"
                >
                    Join TopGrade London FC
                </h2>
                <p
                    class="text-tg-paper mx-auto max-w-xl text-sm leading-relaxed opacity-90 sm:text-base"
                >
                    Come see our coaches in action. Book an introductory trial
                    session for your child at our London training ground.
                </p>
                <div class="pt-2">
                    <Link
                        href="/bookings/free-trial-session"
                        class="tg-btn text-xs"
                    >
                        <span>Book an Introductory Trial</span>
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </section>

        <ImagePreviewModal
            v-model:open="previewModalOpen"
            :image="previewImage"
        />
    </div>
</template>
