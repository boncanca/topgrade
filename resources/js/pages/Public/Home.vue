<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import BlockRenderer from '@/components/CMS/BlockRenderer.vue';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import {
    ArrowRight,
    Clock,
    MapPin,
    CheckCircle2,
    Calendar,
    Sparkles,
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
    booking_label?: string;
}

interface Block {
    id: number;
    uuid: string;
    type: string;
    payload: any;
    settings?: any;
}

interface PageContent {
    id: number;
    title: string;
    slug: string;
    excerpt?: string;
    content?: string;
}

const props = defineProps<{
    featuredActivities?: Activity[];
    page?: PageContent | null;
    blocks?: Block[];
}>();

defineOptions({
    layout: PublicLayout,
});

const quickStats = [
    { label: 'Age Groups', value: 'U7 – U16', detail: 'Youth football squads' },
    { label: 'Weekly Training', value: '3 Days', detail: 'Tuesdays, Wednesdays & Thursdays' },
    { label: 'Home Grounds', value: '3 Venues', detail: 'Tottenham & Hackney' },
    { label: 'Affiliation', value: 'London FA', detail: 'Sanctioned Youth Leagues' },
];

const pillars = [
    {
        num: '01',
        title: 'Technical Skill',
        description: 'First touch, passing range, striking execution, and 1v1 attacking confidence.',
    },
    {
        num: '02',
        title: 'Game Understanding',
        description: 'Reading game situations, tactical positioning, and rapid decision-making under pressure.',
    },
    {
        num: '03',
        title: 'Teamwork & Discipline',
        description: 'Punctuality, structured preparation, pitch communication, and playing for the team.',
    },
    {
        num: '04',
        title: 'Playing Experience',
        description: 'Competitive minutes in sanctioned London youth leagues, not a season on the bench.',
    },
];

// Club Informational Training Schedule
const trainingSchedule = [
    {
        days: 'Tuesdays & Thursdays',
        badge: 'Midweek Training',
        sessions: [
            { age: 'U7 – U12', time: '5:00 – 7:00 PM' },
            { age: 'U13 – U16', time: '6:30 – 8:00 PM' },
        ],
        venue: {
            name: 'Frederick Knight Sports Centre',
            facility: 'Tottenham Powerleague',
            address: 'Willoughby Lane, Tottenham, London N17 0RT',
            surface: 'All-Weather 3G Floodlit Pitches',
        },
    },
    {
        days: 'Wednesdays',
        badge: 'Technical Base',
        sessions: [
            { age: 'U7 – U12', time: '5:30 – 7:00 PM' },
        ],
        venue: {
            name: 'Tottenham Community Sports Centre',
            facility: 'Community Sports Facility',
            address: '701–703 High Road, London N17 8AD',
            surface: 'Indoor Sports Hall & Technical Base',
        },
    },
];

// Squads are informational club entities with varied editorial visual rhythm
const squads = [
    {
        name: 'U7 – U8',
        stage: 'Foundation Phase',
        description: 'First touch, ball mastery, agility, balance, and introducing team play in a positive environment.',
        image: '/images/club/youth_match_action.jpg',
        layoutClass: 'md:col-span-5',
        aspectClass: 'aspect-[4/3] md:aspect-[3/4]',
        cropPosition: 'object-center',
    },
    {
        name: 'U9 – U10',
        stage: 'Skill Acquisition',
        description: '1v1 attacking and defending, spatial awareness, passing range, and instinctive game decisions.',
        image: '/484192970_1108922961247045_3935375872642678849_n.jpg',
        layoutClass: 'md:col-span-7 md:pt-12',
        aspectClass: 'aspect-[16/10]',
        cropPosition: 'object-top',
    },
    {
        name: 'U11 – U12',
        stage: 'Game Development',
        description: 'Tactical positioning, transition play, set-pieces, spatial compactness, and match tempo management.',
        image: '/485087659_1108923127913695_5031817050217357486_n.jpg',
        layoutClass: 'md:col-span-8',
        aspectClass: 'aspect-[16/9] md:aspect-[21/10]',
        cropPosition: 'object-center',
    },
    {
        name: 'U13 – U14',
        stage: 'Youth Progression',
        description: 'Tactical discipline, physical conditioning, competitive league fixtures, and structured pitch mentoring.',
        image: '/images/club/tactical_coaching.jpg',
        layoutClass: 'md:col-span-4 md:-mt-8',
        aspectClass: 'aspect-[3/4]',
        cropPosition: 'object-center',
    },
    {
        name: 'U15 – U16',
        stage: 'Youth Competition',
        description: 'Competitive match play, high-intensity game management, leadership, and senior club pathway preparation.',
        image: '/images/club/squad_celebration.jpg',
        layoutClass: 'md:col-span-12',
        aspectClass: 'aspect-[16/9] md:aspect-[2.4/1]',
        cropPosition: 'object-center',
    },
];

// Editorial Moments Ribbon — Pure photography, restrained typography
const momentsRibbon = [
    {
        num: '01',
        title: 'TRAINING',
        image: '/images/club/club-training-london.jpg',
        aspect: 'aspect-[3/4]',
        offsetClass: 'lg:translate-y-0',
    },
    {
        num: '02',
        title: 'MATCHDAY',
        image: '/484977737_1109608517845156_6730033439051003629_n.jpg',
        aspect: 'aspect-[4/5]',
        offsetClass: 'lg:translate-y-16',
    },
    {
        num: '03',
        title: 'TEAMWORK',
        image: '/images/club/squad_celebration.jpg',
        aspect: 'aspect-[3/4]',
        offsetClass: 'lg:translate-y-6',
    },
    {
        num: '04',
        title: 'DEVELOPMENT',
        image: '/484192970_1108922961247045_3935375872642678849_n.jpg',
        aspect: 'aspect-[4/5]',
        offsetClass: 'lg:translate-y-24',
    },
    {
        num: '05',
        title: 'COACHING',
        image: '/images/club/tactical_coaching.jpg',
        aspect: 'aspect-[3/4]',
        offsetClass: 'lg:translate-y-8',
    },
    {
        num: '06',
        title: 'FOOTBALL',
        image: '/images/club/floodlit-match.png',
        aspect: 'aspect-[4/5]',
        offsetClass: 'lg:translate-y-20',
    },
];

// Home Matchday Grounds
const homeVenues = [
    {
        num: '01',
        name: 'Frederick Knight Sports Centre',
        area: 'Tottenham · N17 0RT',
        address: 'Willoughby Lane, Tottenham',
        role: 'Home Games & Midweek Floodlit Pitches',
    },
    {
        num: '02',
        name: 'Mabley Green',
        area: 'Homerton · E9 5HW',
        address: 'Lee Conservancy Road, Hackney',
        role: 'Home Matchday Venue',
    },
    {
        num: '03',
        name: 'Hackney Marshes',
        area: 'Hackney · E9 5PF',
        address: 'Homerton Road, Hackney',
        role: 'Home Matchday Grass Pitches',
    },
];

/* ─── Match Ball Choreography (Subordinate to Content & Integrated with Translucent Surfaces) ─── */
let animFrameId: number | null = null;
let io: IntersectionObserver | null = null;

onMounted(async () => {
    await nextTick();

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const root = document.documentElement;
    const ball = document.getElementById('ball');
    const backdrop = document.getElementById('creed-backdrop');

    // Scroll Reveal IntersectionObserver
    io = new IntersectionObserver(
        (entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) {
                    e.target.classList.add('in');
                    io?.unobserve(e.target);
                }
            });
        },
        { threshold: 0.10, rootMargin: '0px 0px -4% 0px' }
    );

    document.querySelectorAll('[data-reveal]').forEach((el) => io?.observe(el));

    if (reduceMotion || !ball) {
        if (ball) {
            ball.style.transform = 'translate3d(-50%, calc(-50% + 30vh), 0) scale(0.95)';
            ball.style.opacity = '0.9';
        }
        root.style.setProperty('--zoom', '1');
        return;
    }

    /*
     * Ball Waypoints (Atmospheric Visual Thread):
     * Ball floats in the background space and stays perceptible through subtle translucent surfaces,
     * routing gracefully through negative space.
     */
    const STOPS = [
        { sec: 'hero', at: 0.00, x: 0, y: 32, s: 0.95, r: 0, o: 0.92, p: 0.22, m: { y: 28, s: 0.65, o: 0.80 } },
        { sec: 'hero', at: 0.85, x: -38, y: 28, s: 0.42, r: 160, o: 0.60, p: 0.14, m: { x: -30, y: 28, s: 0.30, o: 0.45 } },
        { sec: 'develop', at: 0.50, x: -42, y: 8, s: 0.38, r: 250, o: 0.38, p: 0.06, m: { x: -36, y: 12, s: 0.26, o: 0.25 } },
        { sec: 'creed', at: 0.50, x: 0, y: 0, s: 2.90, r: 430, o: 1.00, p: 0.22, m: { s: 2.35 } },
        { sec: 'train', at: 0.35, x: 40, y: -8, s: 0.36, r: 580, o: 0.35, p: 0.06, m: { x: 34, y: -12, s: 0.24, o: 0.20 } },
        { sec: 'teams', at: 0.50, x: -38, y: 0, s: 0.38, r: 690, o: 0.36, p: 0.06, m: { x: -32, y: -6, s: 0.24, o: 0.20 } },
        { sec: 'gallery', at: 0.45, x: 36, y: 4, s: 0.40, r: 780, o: 0.38, p: 0.06, m: { x: 30, y: 6, s: 0.25, o: 0.22 } },
        { sec: 'matchday', at: 0.50, x: -36, y: 16, s: 0.38, r: 870, o: 0.35, p: 0.06, m: { x: -30, y: 18, s: 0.24, o: 0.20 } },
        { sec: 'cta', at: 0.50, x: 0, y: 35, s: 0.75, r: 960, o: 0.85, p: 0.18, m: { y: 32, s: 0.55, o: 0.65 } }
    ];

    interface Point {
        y: number;
        x: number;
        ty: number;
        s: number;
        r: number;
        o: number;
        p: number;
    }

    let pts: Point[] = [];

    const buildPoints = () => {
        const mob = window.innerWidth < 880;
        pts = STOPS.map((k) => {
            const el = document.getElementById(k.sec);
            if (!el) return null;
            const top = el.getBoundingClientRect().top + window.scrollY;
            const o: Point = {
                y: top + k.at * el.offsetHeight,
                x: k.x,
                ty: k.y,
                s: k.s,
                r: k.r,
                o: k.o,
                p: k.p,
            };
            if (mob && k.m) {
                if (k.m.x !== undefined) o.x = k.m.x;
                if (k.m.y !== undefined) o.ty = k.m.y;
                if (k.m.s !== undefined) o.s = k.m.s;
                if (k.m.o !== undefined) o.o = k.m.o;
            }
            return o;
        }).filter(Boolean) as Point[];

        pts.sort((a, b) => a.y - b.y);
    };

    const ease = (t: number) => t * t * (3 - 2 * t);
    const lerp = (a: number, b: number, t: number) => a + (b - a) * t;

    let targetScrollY = window.scrollY;
    let currentScrollY = targetScrollY;

    const onScroll = () => {
        targetScrollY = window.scrollY;
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', buildPoints);
    buildPoints();

    const pars = Array.from(document.querySelectorAll<HTMLElement>('[data-par]'));

    const animate = (time: number) => {
        // Damped interpolation loop without scroll hijacking
        currentScrollY += (targetScrollY - currentScrollY) * 0.075;
        const sy = currentScrollY + window.innerHeight * 0.5;

        if (pts.length > 0) {
            let a = pts[0];
            let b = pts[pts.length - 1];
            let t = 0;

            if (sy <= pts[0].y) {
                a = b = pts[0];
            } else if (sy >= b.y) {
                a = b = pts[pts.length - 1];
            } else {
                for (let i = 0; i < pts.length - 1; i++) {
                    if (sy >= pts[i].y && sy <= pts[i + 1].y) {
                        a = pts[i];
                        b = pts[i + 1];
                        t = ease((sy - a.y) / Math.max(1, b.y - a.y));
                        break;
                    }
                }
            }

            const x = lerp(a.x, b.x, t);
            const y = lerp(a.ty, b.ty, t);
            const s = lerp(a.s, b.s, t);
            const r = lerp(a.r, b.r, t);
            const op = lerp(a.o, b.o, t);
            const pi = lerp(a.p, b.p, t);

            // Stadium micro-hover
            const hoverY = Math.sin(time * 0.0012) * 0.35;
            const hoverRot = Math.sin(time * 0.0008) * 0.8;

            const finalY = y + hoverY;
            const finalRot = r + hoverRot;

            ball.style.transform = `translate3d(calc(-50% + ${x.toFixed(2)}vw), calc(-50% + ${finalY.toFixed(2)}vh), 0) scale(${s.toFixed(3)}) rotate(${finalRot.toFixed(1)}deg)`;
            ball.style.opacity = op.toFixed(3);

            const zoomVal = Math.min(1, Math.max(0, (s - 1.5) / 1.1));
            root.style.setProperty('--tg-pitch-opacity', pi.toFixed(3));
            root.style.setProperty('--zoom', zoomVal.toFixed(3));

            if (backdrop) {
                backdrop.style.opacity = (zoomVal * 0.96).toFixed(3);
            }
        }

        // Parallax elements
        pars.forEach((el) => {
            const rc = el.getBoundingClientRect();
            const off = rc.top + rc.height / 2 - window.innerHeight / 2;
            const factor = parseFloat(el.dataset.par || '0');
            el.style.transform = `translate3d(0, ${(-off * factor).toFixed(1)}px, 0)`;
        });

        animFrameId = requestAnimationFrame(animate);
    };

    animFrameId = requestAnimationFrame(animate);
});

onUnmounted(() => {
    if (animFrameId) {
        cancelAnimationFrame(animFrameId);
    }
    io?.disconnect();
});
</script>

<template>
    <Head>
        <title>TopGrade London FC — Official Youth Football Club</title>
        <meta
            name="description"
            content="TopGrade London FC provides structured youth football training, technical coaching, and league match play for ages U7 to U16 in Tottenham and Hackney. Book a trial session."
        />
    </Head>

    <div class="relative overflow-hidden bg-tg-bg text-tg-text selection:bg-tg-accent selection:text-white">
        <!-- Creed Zoom Dimming Backdrop -->
        <div
            id="creed-backdrop"
            class="creed-backdrop fixed inset-0 pointer-events-none transition-opacity duration-200"
            style="background: rgba(8, 4, 15, 0.94); z-index: 2; opacity: 0;"
            aria-hidden="true"
        />

        <!-- Official Match Ball Physics Layer -->
        <div
            id="ball"
            class="ball-layer fixed left-1/2 top-1/2 pointer-events-none will-change-transform"
            style="z-index: 3;"
            aria-hidden="true"
        >
            <img
                src="/ball-optimized.webp"
                alt="TopGrade London FC Match Ball"
                class="tgball-img"
                style="
                    width: var(--tg-ball-size);
                    height: auto;
                    filter: drop-shadow(var(--tg-ball-shadow));
                "
            />
        </div>

        <!-- 00 — OFFICIAL CLUB STATUS BAR -->
        <div
            class="relative z-10 text-xs py-2.5 px-4 sm:px-6 lg:px-8 bg-tg-bg-deep/90 border-b border-tg-border text-tg-text-muted"
        >
            <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="inline-block w-2 h-2 rounded-full" style="background: var(--tg-accent);" />
                    <span class="font-bold tracking-wider uppercase text-tg-text-strong">TopGrade London FC</span>
                    <span class="hidden sm:inline text-tg-border">•</span>
                    <span class="hidden sm:inline">North &amp; East London Youth Football Club</span>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <span class="text-tg-text-strong">London FA Affiliated</span>
                    <span class="text-tg-border">•</span>
                    <span>Community Interest Company (CIC)</span>
                </div>
            </div>
        </div>

        <!-- 01 — HERO SECTION: Monumental Centered Architecture -->
        <section
            id="hero"
            class="relative min-h-[92vh] sm:min-h-[105vh] flex flex-col items-center justify-start text-center pt-16 sm:pt-28 pb-16 sm:pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden"
            style="z-index: 4;"
        >
            <div class="max-w-5xl mx-auto flex flex-col items-center relative z-10">
                <!-- Drop Animated Crest -->
                <div class="mb-3 sm:mb-4 animate-hero-drop">
                    <div class="bg-white rounded p-1.5 shadow-sm inline-block">
                        <img
                            src="/logo.png"
                            alt="TopGrade London FC Crest"
                            class="w-14 sm:w-20 md:w-24 h-auto"
                        />
                    </div>
                </div>

                <!-- Tagline Badge -->
                <div class="mb-3 sm:mb-4">
                    <span
                        class="inline-block px-3 py-1 text-[11px] sm:text-xs font-bold uppercase tracking-widest rounded bg-tg-bg-deep/80 border border-tg-border text-tg-accent"
                    >
                        Youth Football Club · London
                    </span>
                </div>

                <!-- Monumental Headline -->
                <h1
                    class="font-normal uppercase tracking-tight mb-4 sm:mb-6 max-w-4xl text-tg-text-strong"
                    style="
                        font-family: var(--tg-display);
                        font-size: clamp(2.8rem, 13vw, 8.5rem);
                        line-height: 0.88;
                    "
                >
                    <span class="inline-block animate-hero-rise">Top</span><span class="inline-block animate-hero-rise" style="animation-delay: 0.1s;">grade</span><br />
                    <span class="inline-block animate-hero-rise" style="animation-delay: 0.2s;">London FC</span>
                </h1>

                <!-- Subheadline -->
                <p
                    class="text-sm sm:text-lg max-w-2xl mx-auto mb-8 sm:mb-10 leading-relaxed font-normal text-tg-text animate-hero-fade px-2"
                >
                    Youth football in North and East London, built through technical coaching, teamwork, and real competitive league match play for players aged U7 to U16.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full sm:w-auto justify-center mb-10 sm:mb-12 relative z-20 animate-hero-fade" style="animation-delay: 0.5s;">
                    <Link
                        href="/bookings/free-trial-session"
                        class="tg-btn tg-focus shadow-sm justify-center"
                    >
                        <span>Book a Trial</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                    <a
                        href="#train"
                        class="tg-btn ghost tg-focus justify-center"
                    >
                        <span>Training Schedule</span>
                    </a>
                </div>

                <!-- Facts Bar -->
                <div
                    class="w-full max-w-4xl grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 pt-6 sm:pt-8 border-t border-tg-border"
                >
                    <div
                        v-for="stat in quickStats"
                        :key="stat.label"
                        class="p-3.5 sm:p-4 rounded text-center bg-tg-bg-deep/80 border border-tg-border"
                    >
                        <div
                            class="text-lg sm:text-2xl font-normal uppercase text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            {{ stat.value }}
                        </div>
                        <div
                            class="text-[11px] sm:text-xs font-bold uppercase tracking-wider mt-1 text-tg-accent"
                        >
                            {{ stat.label }}
                        </div>
                        <div class="text-[10px] sm:text-[11px] mt-0.5 text-tg-text-muted">
                            {{ stat.detail }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cutout Player with Parallax (Accents side on desktop, positioned cleanly behind content on mobile) -->
            <img
                src="/hero_player_cutout.webp"
                alt="TopGrade player action"
                class="absolute right-[-10%] sm:right-[0%] top-[40%] sm:top-[28%] w-[60vw] sm:w-[35vw] max-w-[480px] pointer-events-none opacity-30 sm:opacity-85 select-none transition-transform duration-300"
                style="z-index: 1;"
                data-par="0.05"
            />

            <!-- Scroll Cue -->
            <div
                class="absolute bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest select-none text-tg-text-muted"
            >
                <span class="w-[1.5px] h-5 sm:h-6 animate-pulse" style="background: var(--tg-accent);" />
                <span>Scroll</span>
            </div>
        </section>

        <!-- 02 — MARQUEE TICKER: Energetic Club Identity -->
        <div
            class="overflow-hidden py-3 relative z-10 bg-tg-purple-deep/90 border-y border-tg-border"
            aria-hidden="true"
        >
            <div class="marquee-track flex gap-12 whitespace-nowrap text-white font-normal" style="font-family: var(--tg-display); font-size: 1.35rem; letter-spacing: 0.05em;">
                <div class="flex items-center gap-12 shrink-0 animate-marquee">
                    <span>ALWAYS THE BEST</span>
                    <span class="text-tg-highlight">★</span>
                    <span>EST. 2022</span>
                    <span class="text-tg-highlight">★</span>
                    <span>TOTTENHAM</span>
                    <span class="text-tg-highlight">★</span>
                    <span>U7–U16</span>
                    <span class="text-tg-highlight">★</span>
                    <span>HACKNEY MATCHDAYS</span>
                    <span class="text-tg-highlight">★</span>
                    <span>ALWAYS THE BEST</span>
                    <span class="text-tg-highlight">★</span>
                    <span>EST. 2022</span>
                    <span class="text-tg-highlight">★</span>
                    <span>TOTTENHAM</span>
                    <span class="text-tg-highlight">★</span>
                    <span>U7–U16</span>
                    <span class="text-tg-highlight">★</span>
                    <span>HACKNEY MATCHDAYS</span>
                    <span class="text-tg-highlight">★</span>
                </div>
            </div>
        </div>

        <!-- 03 — DEVELOP YOUR GAME: 01-04 Pillars + Action Photo -->
        <section
            id="develop"
            class="py-20 sm:py-32 px-4 sm:px-6 lg:px-8 relative z-10 bg-tg-bg/92 border-b border-tg-border"
        >
            <div class="max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                    <!-- Left: Philosophy and 4 Pillars -->
                    <div class="lg:col-span-7">
                        <p class="tg-eyebrow" data-reveal>What We Do</p>
                        <h2
                            data-reveal
                            style="--d: 60ms; font-family: var(--tg-display); font-size: clamp(2.4rem, 6vw, 4.8rem);"
                            class="mb-4 text-tg-text-strong"
                        >
                            Develop<br />Your Game
                        </h2>
                        <p
                            data-reveal
                            style="--d: 110ms;"
                            class="text-base sm:text-lg mb-8 max-w-xl text-tg-text"
                        >
                            Every training session is coached around four core fundamentals. Players work on all of them, across every age group, every week.
                        </p>

                        <!-- Pillars List -->
                        <div class="space-y-0">
                            <div
                                v-for="(pillar, idx) in pillars"
                                :key="pillar.num"
                                data-reveal
                                :style="{ '--d': `${140 + idx * 50}ms` }"
                                class="flex items-start gap-4 py-4 border-t border-tg-border"
                            >
                                <span
                                    class="font-normal text-xl sm:text-2xl min-w-[2.4rem] pt-0.5 text-tg-accent"
                                    style="font-family: var(--tg-display);"
                                >
                                    {{ pillar.num }}
                                </span>
                                <div>
                                    <h3
                                        class="font-normal text-lg sm:text-xl uppercase text-tg-text-strong"
                                        style="font-family: var(--tg-display);"
                                    >
                                        {{ pillar.title }}
                                    </h3>
                                    <p class="text-sm mt-1 text-tg-text-muted">
                                        {{ pillar.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Training Pitch Action Photography -->
                    <div class="lg:col-span-5" data-reveal="right" style="--d: 120ms;">
                        <div class="tg-slot tg-slot tall rounded shadow-lg overflow-hidden border border-tg-border">
                            <img
                                src="/images/club/player-match-action.jpg"
                                alt="TopGrade player in match training action"
                                data-par="0.06"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 04 — CREED SECTION: Dominant Monumental Payoff -->
        <section
            id="creed"
            class="min-h-[90vh] sm:min-h-[100vh] flex flex-col items-center justify-center text-center px-4 relative"
            style="z-index: 5;"
        >
            <div class="max-w-3xl mx-auto relative z-10">
                <h2
                    class="font-normal uppercase tracking-tight mb-4 text-tg-text-strong"
                    style="
                        font-family: var(--tg-display);
                        font-size: clamp(2.8rem, 9vw, 7.5rem);
                        line-height: 0.9;
                    "
                >
                    Always the best
                </h2>
                <p
                    class="text-xs sm:text-lg uppercase tracking-[0.2em] font-semibold text-tg-highlight"
                >
                    Training · Teamwork · Playing Experience
                </p>
            </div>
        </section>

        <!-- 05 — TRAIN WITH US: Editorial Club Timetable + Available Sessions -->
        <section
            id="train"
            class="py-20 sm:py-32 px-4 sm:px-6 lg:px-8 relative z-10 bg-tg-bg/90 border-y border-tg-border"
        >
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6 pb-6 border-b border-tg-border">
                    <div>
                        <p class="tg-eyebrow" data-reveal>Where &amp; When We Train</p>
                        <h2
                            data-reveal
                            style="--d: 60ms; font-family: var(--tg-display); font-size: clamp(2.4rem, 6vw, 4.5rem);"
                            class="text-tg-text-strong"
                        >
                            Train With Us
                        </h2>
                    </div>
                    <p data-reveal style="--d: 100ms;" class="text-sm max-w-md text-tg-text-muted">
                        Weekly club training in Tottenham for youth players aged U7 to U16. Bring boots, shin guards, and water.
                    </p>
                </div>

                <!-- Club Training Timetable (Editorial Presentation) -->
                <div class="grid md:grid-cols-2 gap-6 sm:gap-10 mb-16">
                    <div
                        v-for="(sched, idx) in trainingSchedule"
                        :key="sched.days"
                        data-reveal
                        :style="{ '--d': `${120 + idx * 80}ms` }"
                        class="p-6 sm:p-8 rounded-sm flex flex-col justify-between space-y-6 bg-tg-bg-deep/80 border border-tg-border"
                    >
                        <div class="space-y-6">
                            <!-- Day & Badge -->
                            <div class="flex items-center justify-between pb-4 border-b border-tg-border">
                                <h3
                                    class="text-2xl sm:text-3xl font-normal uppercase text-tg-text-strong"
                                    style="font-family: var(--tg-display);"
                                >
                                    {{ sched.days }}
                                </h3>
                                <span
                                    class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider rounded-xs bg-tg-bg border border-tg-border text-tg-accent"
                                >
                                    {{ sched.badge }}
                                </span>
                            </div>

                            <!-- Sessions & Times (Clean, scannable timetable) -->
                            <div class="space-y-2.5">
                                <div
                                    v-for="(session, sIdx) in sched.sessions"
                                    :key="sIdx"
                                    class="flex items-center justify-between py-2 border-b border-tg-border/60"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <span class="font-bold text-base sm:text-lg text-tg-text-strong">{{ session.age }}</span>
                                    </div>
                                    <span class="text-sm sm:text-base font-semibold text-tg-accent">{{ session.time }}</span>
                                </div>
                            </div>

                            <!-- Venue Info -->
                            <div class="space-y-1.5 pt-2">
                                <div class="font-bold text-base text-tg-text-strong">
                                    {{ sched.venue.name }}
                                </div>
                                <div class="text-xs font-medium text-tg-accent">
                                    {{ sched.venue.facility }}
                                </div>
                                <div class="flex items-start gap-1.5 text-xs text-tg-text-muted">
                                    <MapPin class="w-3.5 h-3.5 shrink-0 mt-0.5 text-tg-border-strong" />
                                    <span>{{ sched.venue.address }}</span>
                                </div>
                                <div class="text-[11px] text-tg-text-muted pt-0.5">
                                    <span class="font-semibold text-tg-text">Surface:</span> {{ sched.venue.surface }}
                                </div>
                            </div>
                        </div>

                        <!-- Action -->
                        <div class="pt-4 border-t border-tg-border">
                            <Link
                                href="/bookings/free-trial-session"
                                class="tg-link flex items-center justify-between text-sm text-tg-accent"
                            >
                                <span>Book an Introductory Trial</span>
                                <ArrowRight class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Contextual Bookable Sessions (If active in database) -->
                <div v-if="featuredActivities && featuredActivities.length > 0" class="mt-8 pt-10 border-t border-tg-border">
                    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-widest block mb-1 text-tg-accent">
                                Join a Session
                            </span>
                            <h3
                                class="text-2xl sm:text-3xl font-normal uppercase text-tg-text-strong"
                                style="font-family: var(--tg-display);"
                            >
                                Available Sessions
                            </h3>
                        </div>
                        <Link
                            href="/bookings"
                            class="tg-link flex items-center gap-1.5 text-xs text-tg-accent"
                        >
                            <span>View All Sessions</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="item in featuredActivities"
                            :key="item.id"
                            data-reveal
                            class="p-6 rounded-sm flex flex-col justify-between bg-tg-bg-deep/80 border border-tg-border transition-all hover:border-tg-border-strong"
                        >
                            <div>
                                <h4
                                    class="text-xl font-normal uppercase mb-2 text-tg-text-strong"
                                    style="font-family: var(--tg-display);"
                                >
                                    {{ item.name }}
                                </h4>
                                <p class="text-xs line-clamp-3 leading-relaxed mb-6 text-tg-text-muted">
                                    {{ item.description }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-tg-border">
                                <Link
                                    :href="`/bookings/${item.slug}`"
                                    class="tg-btn ghost w-full justify-center text-xs"
                                >
                                    <span>{{ item.slug.includes('trial') ? 'Book a Trial' : 'Book Session' }}</span>
                                    <ArrowRight class="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wide Training Pitch Photography -->
                <div class="mt-14" data-reveal style="--d: 200ms;">
                    <div class="tg-slot tg-slot wide rounded-sm shadow-lg overflow-hidden border border-tg-border">
                        <img
                            src="/images/club/training_pitch_evening.jpg"
                            alt="TopGrade evening squad training pitch"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- 06 — OUR TEAMS: Asymmetric Editorial Squad Photography (Informational Club Entities) -->
        <section
            id="teams"
            class="py-20 sm:py-32 px-4 sm:px-6 lg:px-8 relative z-10 bg-tg-bg/90 border-b border-tg-border"
        >
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6 pb-6 border-b border-tg-border">
                    <div>
                        <p class="tg-eyebrow" data-reveal>Our Teams</p>
                        <h2
                            data-reveal
                            style="--d: 60ms; font-family: var(--tg-display); font-size: clamp(2.4rem, 6vw, 4.5rem);"
                            class="text-tg-text-strong"
                        >
                            Built Through The Game.
                        </h2>
                    </div>
                    <p data-reveal style="--d: 100ms;" class="text-sm max-w-md text-tg-text-muted">
                        Structured progression from Foundation Phase through to competitive London youth league football.
                    </p>
                </div>

                <!-- True Asymmetric Editorial Visual Rhythm -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-start">
                    <div
                        v-for="(squad, idx) in squads"
                        :key="squad.name"
                        :class="[squad.layoutClass, 'group flex flex-col space-y-3']"
                        data-reveal
                        :style="{ '--d': `${80 + idx * 50}ms` }"
                    >
                        <!-- Varied Aspect Ratio Photo Container -->
                        <div :class="[squad.aspectClass, 'relative w-full overflow-hidden rounded-sm bg-tg-bg-deep border border-tg-border shadow-sm']">
                            <img
                                :src="squad.image"
                                :alt="`TopGrade London FC ${squad.name} Squad`"
                                :class="['w-full h-full object-cover transition-transform duration-700 group-hover:scale-105', squad.cropPosition]"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none" />

                            <!-- Bottom Tag inside Image -->
                            <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between pointer-events-none">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-tg-accent block mb-0.5">
                                        {{ squad.stage }}
                                    </span>
                                    <h3
                                        class="font-normal uppercase text-3xl sm:text-4xl text-white leading-none"
                                        style="font-family: var(--tg-display);"
                                    >
                                        {{ squad.name }}
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <!-- Supporting Editorial Copy Beneath -->
                        <p class="text-xs leading-relaxed text-tg-text-muted px-1">
                            {{ squad.description }}
                        </p>
                    </div>
                </div>

                <!-- Single Section-Level Contextual CTA -->
                <div class="mt-16 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-tg-border">
                    <div class="flex items-center gap-2.5 text-xs text-tg-text-muted">
                        <CheckCircle2 class="w-4 h-4 shrink-0 text-tg-accent" />
                        <span>Competitive youth squads participating in London FA sanctioned youth leagues.</span>
                    </div>

                    <a
                        href="#train"
                        class="tg-link flex items-center gap-2 text-sm text-tg-accent"
                    >
                        <span>Looking to join TopGrade? Find a session</span>
                        <ArrowRight class="w-4 h-4" />
                    </a>
                </div>
            </div>
        </section>

        <!-- 07 — MOMENTS: Endless Photographic Ribbon -->
        <section
            id="gallery"
            class="py-20 sm:py-32 px-4 sm:px-6 lg:px-8 relative z-10 bg-tg-bg/88 border-b border-tg-border"
        >
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6 pb-6 border-b border-tg-border">
                    <div>
                        <p class="tg-eyebrow" data-reveal>The Game</p>
                        <h2
                            data-reveal
                            style="--d: 60ms; font-family: var(--tg-display); font-size: clamp(2.4rem, 6vw, 4.5rem);"
                            class="text-tg-text-strong"
                        >
                            Moments
                        </h2>
                    </div>
                    <p data-reveal style="--d: 100ms;" class="text-sm max-w-md text-tg-text-muted">
                        Training. Matchday. The moments in between.
                    </p>
                </div>

                <!-- Endless Photographic Ribbon (Offset stream, minimal typography) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                    <div
                        v-for="(moment, mIdx) in momentsRibbon"
                        :key="moment.num"
                        :class="['group flex flex-col space-y-2.5 transition-transform duration-300', moment.offsetClass]"
                        data-reveal
                        :style="{ '--d': `${100 + mIdx * 60}ms` }"
                    >
                        <!-- Vertical Card Image -->
                        <div
                            :class="[
                                moment.aspect,
                                'relative w-full rounded-sm overflow-hidden bg-tg-bg-deep border border-tg-border shadow-md'
                            ]"
                        >
                            <img
                                :src="moment.image"
                                :alt="moment.title"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent pointer-events-none" />

                            <div class="absolute bottom-4 left-4 right-4 pointer-events-none flex items-center justify-between">
                                <span
                                    class="font-normal uppercase text-lg sm:text-xl text-white tracking-wider"
                                    style="font-family: var(--tg-display);"
                                >
                                    {{ moment.title }}
                                </span>
                                <span class="text-xs font-mono font-bold text-tg-accent">{{ moment.num }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 08 — MATCHDAY: Where The Football Happens -->
        <section
            id="matchday"
            class="py-20 sm:py-32 px-4 sm:px-6 lg:px-8 relative z-10 bg-tg-bg/90 border-b border-tg-border"
        >
            <div class="max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                    <!-- Left Column: Editorial Venue List -->
                    <div class="lg:col-span-6 space-y-8">
                        <div>
                            <p class="tg-eyebrow" data-reveal>Competition</p>
                            <h2
                                data-reveal
                                style="--d: 60ms; font-family: var(--tg-display); font-size: clamp(2.4rem, 6vw, 4.5rem);"
                                class="text-tg-text-strong mb-4"
                            >
                                Matchday
                            </h2>
                            <p data-reveal style="--d: 100ms;" class="text-sm max-w-md text-tg-text-muted">
                                Where TopGrade plays home football across North and East London.
                            </p>
                        </div>

                        <!-- Numbered Home Venues -->
                        <div class="space-y-0">
                            <div
                                v-for="(venue, vIdx) in homeVenues"
                                :key="venue.num"
                                data-reveal
                                :style="{ '--d': `${140 + vIdx * 50}ms` }"
                                class="py-5 border-t border-tg-border space-y-1"
                            >
                                <div class="flex items-baseline justify-between gap-4">
                                    <h3
                                        class="text-xl sm:text-2xl font-normal uppercase text-tg-text-strong"
                                        style="font-family: var(--tg-display);"
                                    >
                                        {{ venue.name }}
                                    </h3>
                                    <span class="text-xs font-mono font-bold text-tg-accent">{{ venue.num }}</span>
                                </div>
                                <div class="text-xs font-semibold text-tg-accent">
                                    {{ venue.area }}
                                </div>
                                <div class="text-xs text-tg-text-muted">
                                    {{ venue.address }} · {{ venue.role }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Single Dominant Match Photograph -->
                    <div class="lg:col-span-6" data-reveal="right" style="--d: 150ms;">
                        <div class="tg-slot tg-slot tall rounded-sm shadow-xl overflow-hidden border border-tg-border">
                            <img
                                src="/484977737_1109608517845156_6730033439051003629_n.jpg"
                                alt="TopGrade London FC matchday action"
                                data-par="0.05"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 09 — FINAL CTA: Train With TopGrade -->
        <section
            id="cta"
            class="py-20 sm:py-28 px-4 sm:px-6 lg:px-8 relative z-10 flex justify-center text-center"
        >
            <div
                data-reveal="scale"
                class="cta-frame relative w-full max-w-5xl rounded-sm overflow-hidden flex flex-col items-center justify-center p-10 sm:p-20 text-white shadow-2xl bg-tg-bg-deep border border-tg-border"
            >
                <img
                    src="/images/club/floodlit-match.png"
                    alt="TopGrade football pitch floodlights"
                    class="absolute inset-0 w-full h-full object-cover opacity-25"
                />
                <div class="absolute inset-0 bg-black/60" />

                <div class="relative z-10 max-w-2xl mx-auto space-y-4">
                    <span class="inline-block px-3 py-1 bg-tg-bg border border-tg-border text-tg-accent text-xs font-bold uppercase tracking-widest rounded-xs">
                        TopGrade London FC
                    </span>
                    <h2
                        class="font-normal uppercase tracking-tight text-white"
                        style="font-family: var(--tg-display); font-size: clamp(2.6rem, 7vw, 5.5rem); line-height: 0.9;"
                    >
                        Train With TopGrade
                    </h2>
                    <p class="text-sm sm:text-base text-tg-paper max-w-lg mx-auto opacity-90">
                        Join our weekly training sessions in Tottenham. Structured coaching for youth players aged U7 to U16.
                    </p>
                    <div class="pt-4 flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                        <Link
                            href="/bookings"
                            class="tg-btn tg-focus justify-center"
                        >
                            <span>Find a Session</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                        <Link
                            href="/contact"
                            class="tg-btn ghost tg-focus justify-center"
                        >
                            <span>Contact Coaches</span>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dynamic CMS Content Blocks if any -->
        <div v-if="blocks && blocks.length > 0" class="max-w-7xl mx-auto px-4 py-8 relative z-10">
            <BlockRenderer :blocks="blocks" />
        </div>
    </div>
</template>

<style scoped>
/* Keyframe Hero Drop Animation */
@keyframes heroDrop {
    from {
        opacity: 0;
        transform: translateY(-24px) scale(0.85);
    }
    to {
        opacity: 1;
        transform: none;
    }
}
.animate-hero-drop {
    animation: heroDrop 1s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* Keyframe Hero Typography Rise */
@keyframes heroRise {
    from {
        opacity: 0;
        transform: translateY(0.35em);
    }
    to {
        opacity: 1;
        transform: none;
    }
}
.animate-hero-rise {
    animation: heroRise 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Keyframe Hero Fade */
@keyframes heroFade {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: none;
    }
}
.animate-hero-fade {
    animation: heroFade 1s 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* Marquee Infinite Animation */
@keyframes marqueeSlide {
    to {
        transform: translateX(-50%);
    }
}
.animate-marquee {
    animation: marqueeSlide 30s linear infinite;
}
.marquee-track:hover .animate-marquee {
    animation-play-state: paused;
}

/* Reduced Motion Override */
@media (prefers-reduced-motion: reduce) {
    .animate-hero-drop,
    .animate-hero-rise,
    .animate-hero-fade,
    .animate-marquee {
        animation: none !important;
        transform: none !important;
        opacity: 1 !important;
    }
}
</style>
