<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, MapPin, CheckCircle2, Maximize2 } from '@lucide/vue';
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import BlockRenderer from '@/components/CMS/BlockRenderer.vue';
import ImagePreviewModal from '@/components/Public/ImagePreviewModal.vue';
import type { PreviewImageData } from '@/components/Public/ImagePreviewModal.vue';
import JsonLd from '@/components/SEO/JsonLd.vue';
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

interface HeroProps {
    eyebrow?: string;
    headline?: string;
    description?: string;
    primary_cta?: {
        label: string;
        url: string;
    };
    video_url?: string | null;
    poster_url?: string | null;
}

interface TeamSquad {
    id: number;
    name: string;
    slug: string;
    age_group: string;
    stage: string;
    description: string;
    visual_variant: string;
    image_url?: string;
}

interface MomentItem {
    id: number;
    num: string;
    title: string;
    slug: string;
    description: string;
    image_url?: string;
}

interface TrainingScheduleItem {
    days: string;
    badge: string;
    sessions: Array<{ age: string; time: string }>;
    venue: {
        name: string;
        facility: string;
        address: string;
        surface: string;
    };
}

const props = defineProps<{
    featuredActivities?: Activity[];
    page?: PageContent | null;
    blocks?: Block[];
    hero?: HeroProps;
    pillars?: Array<{ num: string; title: string; description: string }>;
    quickStats?: Array<{ label: string; value: string; detail: string }>;
    squads?: TeamSquad[];
    momentsRibbon?: MomentItem[];
    trainingSchedule?: TrainingScheduleItem[];
}>();

defineOptions({
    layout: PublicLayout,
});

const heroVideoRef = ref<HTMLVideoElement | null>(null);
const videoFailed = ref(false);
const prefersReducedMotion = ref(false);

const heroVideoUrl = computed(
    () => props.hero?.video_url || '/hero-training-loop-topgrade-london-fc.mp4',
);
const heroPosterUrl = computed(
    () =>
        props.hero?.poster_url ||
        '/images/club/hero-matchday-topgrade-london-fc.webp',
);
const heroEyebrow = computed(() => props.hero?.eyebrow || 'More Than Football');
const heroHeadline = computed(
    () => props.hero?.headline || 'Develop Your Football Future',
);
const heroDescription = computed(
    () =>
        props.hero?.description ||
        'Youth football in North and East London, built through technical coaching, teamwork, and real competitive league match play for players aged U7 to U16.',
);
const heroPrimaryCta = computed(
    () =>
        props.hero?.primary_cta || {
            label: 'Book a Trial',
            url: '/bookings/free-trial-session',
        },
);

const previewImage = ref<PreviewImageData | null>(null);
const previewModalOpen = ref(false);

function openImagePreview(data: PreviewImageData): void {
    previewImage.value = data;
    previewModalOpen.value = true;
}

const heroHeadlineParts = computed(() => {
    const text = heroHeadline.value;
    const words = text.split(/\s+/);

    if (words.length >= 4) {
        return [
            words.slice(0, 2).join(' '),
            words[2],
            words.slice(3).join(' '),
        ];
    } else if (words.length === 3) {
        return [words[0], words[1], words[2]];
    }

    return [text];
});

const onVideoError = () => {
    videoFailed.value = true;
};

const quickStats = computed(() => {
    if (props.quickStats && props.quickStats.length > 0) {
        return props.quickStats;
    }

    return [
        {
            label: 'Age Groups',
            value: 'Ages 4–18',
            detail: 'Junior & youth squads',
        },
        {
            label: 'Weekly Training',
            value: '3 Days',
            detail: 'Tuesdays, Wednesdays & Thursdays',
        },
        {
            label: 'Home Grounds',
            value: '3 Venues',
            detail: 'Tottenham & Hackney',
        },
        {
            label: 'Matchday',
            value: 'League',
            detail: 'Sanctioned youth fixtures',
        },
    ];
});

const pillars = computed(() => {
    if (props.pillars && props.pillars.length > 0) {
        return props.pillars;
    }

    return [
        {
            num: '01',
            title: 'Technical Skill',
            description:
                'First touch, passing range, striking execution, and 1v1 attacking confidence.',
        },
        {
            num: '02',
            title: 'Game Understanding',
            description:
                'Reading game situations, tactical positioning, and rapid decision-making under pressure.',
        },
        {
            num: '03',
            title: 'Teamwork & Discipline',
            description:
                'Punctuality, structured preparation, pitch communication, and playing for the team.',
        },
        {
            num: '04',
            title: 'Playing Experience',
            description:
                'Competitive minutes in sanctioned London youth leagues, not a season on the bench.',
        },
    ];
});

// Club Informational Training Schedule
const trainingSchedule = computed(() => {
    if (props.trainingSchedule && props.trainingSchedule.length > 0) {
        return props.trainingSchedule;
    }

    return [
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
            sessions: [{ age: 'U7 – U12', time: '5:30 – 7:00 PM' }],
            venue: {
                name: 'Tottenham Community Sports Centre',
                facility: 'Community Sports Facility',
                address: '701–703 High Road, London N17 8AD',
                surface: 'Indoor Sports Hall & Technical Base',
            },
        },
    ];
});

// Verified Schema.org SportsClub / Organization entity
const sportsClubSchema = {
    '@context': 'https://schema.org',
    '@type': 'SportsClub',
    name: 'TopGrade London FC',
    legalName: 'TOPGRADE LONDON FC CIC',
    url: 'https://topgradelondonfc.co.uk',
    logo: 'https://topgradelondonfc.co.uk/logo.png',
    image: 'https://topgradelondonfc.co.uk/images/og/topgrade-london-fc.jpg',
    description:
        'TopGrade London FC is a youth football club based in Tottenham, London, providing structured coaching, teams, and matchday football for young players aged U7 to U16.',
    email: 'info@topgradelondonfc.co.uk',
    sport: 'Football',
    address: {
        '@type': 'PostalAddress',
        streetAddress: '30 Broadwater Road',
        addressLocality: 'Tottenham',
        addressRegion: 'London',
        postalCode: 'N17 6ES',
        addressCountry: 'GB',
    },
    location: [
        {
            '@type': 'Place',
            name: 'Frederick Knight Sports Centre',
            address: {
                '@type': 'PostalAddress',
                streetAddress: 'Willoughby Lane',
                addressLocality: 'Tottenham',
                postalCode: 'N17 0RT',
                addressCountry: 'GB',
            },
        },
        {
            '@type': 'Place',
            name: 'Tottenham Community Sports Centre',
            address: {
                '@type': 'PostalAddress',
                streetAddress: '701–703 High Road',
                addressLocality: 'Tottenham',
                postalCode: 'N17 8AD',
                addressCountry: 'GB',
            },
        },
    ],
};

const squadVariantStyles: Record<
    string,
    {
        layoutClass: string;
        aspectClass: string;
        cropPosition: string;
        floatY: string;
        rotate: string;
    }
> = {
    foundation: {
        layoutClass: 'md:col-span-5',
        aspectClass: 'aspect-[16/10] sm:aspect-[16/9]',
        cropPosition: 'object-center',
        floatY: '-4px',
        rotate: '-0.3deg',
    },
    acquisition: {
        layoutClass: 'md:col-span-7 md:pt-12',
        aspectClass: 'aspect-[16/10]',
        cropPosition: 'object-top',
        floatY: '-6px',
        rotate: '0.25deg',
    },
    development: {
        layoutClass: 'md:col-span-8',
        aspectClass: 'aspect-[16/9] md:aspect-[21/10]',
        cropPosition: 'object-center',
        floatY: '-5px',
        rotate: '-0.2deg',
    },
    progression: {
        layoutClass: 'md:col-span-4 md:-mt-8',
        aspectClass: 'aspect-[16/10] sm:aspect-[16/9]',
        cropPosition: 'object-center',
        floatY: '-6px',
        rotate: '0.35deg',
    },
    competition: {
        layoutClass: 'md:col-span-12',
        aspectClass: 'aspect-[16/9] md:aspect-[2.4/1]',
        cropPosition: 'object-center',
        floatY: '-4px',
        rotate: '-0.15deg',
    },
};

// Squads are informational club entities with varied editorial visual rhythm
const squads = computed(() => {
    if (props.squads && props.squads.length > 0) {
        return props.squads.map((team, idx) => {
            const variantKey =
                team.visual_variant ||
                [
                    'foundation',
                    'acquisition',
                    'development',
                    'progression',
                    'competition',
                ][idx % 5];
            const style =
                squadVariantStyles[variantKey] || squadVariantStyles.foundation;

            return {
                name: team.name,
                stage: team.stage,
                description: team.description,
                image:
                    team.image_url ||
                    '/images/club/foundation-phase-match-action-topgrade-london-fc.webp',
                ...style,
            };
        });
    }

    return [
        {
            name: 'U7 – U8',
            stage: 'Foundation Phase',
            description:
                'First touch, ball mastery, agility, balance, and introducing team play in a positive environment.',
            image: '/images/club/u7-u8-squad-photo-topgrade-london-fc.webp',
            layoutClass: 'md:col-span-5',
            aspectClass: 'aspect-[16/10] sm:aspect-[16/9]',
            cropPosition: 'object-center',
            floatY: '-4px',
            rotate: '-0.3deg',
        },
        {
            name: 'U9 – U10',
            stage: 'Skill Acquisition',
            description:
                '1v1 attacking and defending, spatial awareness, passing range, and instinctive game decisions.',
            image: '/images/club/skill-acquisition-training-topgrade-london-fc.webp',
            layoutClass: 'md:col-span-7 md:pt-12',
            aspectClass: 'aspect-[16/10]',
            cropPosition: 'object-top',
            floatY: '-6px',
            rotate: '0.25deg',
        },
        {
            name: 'U11 – U12',
            stage: 'Game Development',
            description:
                'Tactical positioning, transition play, set-pieces, spatial compactness, and match tempo management.',
            image: '/images/club/game-development-match-action-topgrade-london-fc.webp',
            layoutClass: 'md:col-span-8',
            aspectClass: 'aspect-[16/9] md:aspect-[21/10]',
            cropPosition: 'object-center',
            floatY: '-5px',
            rotate: '-0.2deg',
        },
        {
            name: 'U13 – U14',
            stage: 'Youth Progression',
            description:
                'Tactical discipline, physical conditioning, competitive league fixtures, and structured pitch mentoring.',
            image: '/images/club/tactical-coaching-briefing-topgrade-london-fc.webp',
            layoutClass: 'md:col-span-4 md:-mt-8',
            aspectClass: 'aspect-[16/10] sm:aspect-[16/9]',
            cropPosition: 'object-center',
            floatY: '-6px',
            rotate: '0.35deg',
        },
        {
            name: 'U15 – U16',
            stage: 'Youth Competition',
            description:
                'Competitive match play, high-intensity game management, leadership, and senior club pathway preparation.',
            image: '/images/club/squad-celebration-trophy-topgrade-london-fc.webp',
            layoutClass: 'md:col-span-12',
            aspectClass: 'aspect-[16/9] md:aspect-[2.4/1]',
            cropPosition: 'object-center',
            floatY: '-4px',
            rotate: '-0.15deg',
        },
    ];
});

const momentsRibbonStyles = [
    {
        aspect: 'aspect-[16/11]',
        offsetClass: 'lg:translate-y-0',
        floatY: '-4px',
        rotate: '-0.35deg',
    },
    {
        aspect: 'aspect-[16/11]',
        offsetClass: 'lg:translate-y-12',
        floatY: '-6px',
        rotate: '0.3deg',
    },
    {
        aspect: 'aspect-[16/11]',
        offsetClass: 'lg:translate-y-4',
        floatY: '-5px',
        rotate: '-0.25deg',
    },
    {
        aspect: 'aspect-[16/11]',
        offsetClass: 'lg:translate-y-16',
        floatY: '-6px',
        rotate: '0.4deg',
    },
    {
        aspect: 'aspect-[16/11]',
        offsetClass: 'lg:translate-y-8',
        floatY: '-4px',
        rotate: '-0.3deg',
    },
    {
        aspect: 'aspect-[16/11]',
        offsetClass: 'lg:translate-y-20',
        floatY: '-5px',
        rotate: '0.25deg',
    },
];

// Editorial Moments Ribbon — Pure photography, restrained typography
const momentsRibbon = computed(() => {
    if (props.momentsRibbon && props.momentsRibbon.length > 0) {
        return props.momentsRibbon.map((m, idx) => {
            const style = momentsRibbonStyles[idx % momentsRibbonStyles.length];

            return {
                num: m.num,
                title: m.title,
                image:
                    m.image_url ||
                    '/images/club/youth-squad-team-topgrade-london-fc.webp',
                ...style,
            };
        });
    }

    return [
        {
            num: '01',
            title: 'TRAINING',
            image: '/images/club/youth-squad-team-topgrade-london-fc.webp',
            aspect: 'aspect-[16/11]',
            offsetClass: 'lg:translate-y-0',
            floatY: '-4px',
            rotate: '-0.35deg',
        },
        {
            num: '02',
            title: 'MATCHDAY',
            image: '/images/club/matchday-fixture-action-topgrade-london-fc.webp',
            aspect: 'aspect-[16/11]',
            offsetClass: 'lg:translate-y-12',
            floatY: '-6px',
            rotate: '0.3deg',
        },
        {
            num: '03',
            title: 'TEAMWORK',
            image: '/images/club/u7-u8-squad-photo-topgrade-london-fc.webp',
            aspect: 'aspect-[16/11]',
            offsetClass: 'lg:translate-y-4',
            floatY: '-5px',
            rotate: '-0.25deg',
        },
        {
            num: '04',
            title: 'DEVELOPMENT',
            image: '/images/club/skill-acquisition-training-topgrade-london-fc.webp',
            aspect: 'aspect-[16/11]',
            offsetClass: 'lg:translate-y-16',
            floatY: '-6px',
            rotate: '0.4deg',
        },
        {
            num: '05',
            title: 'COACHING',
            image: '/images/club/tactical-coaching-briefing-topgrade-london-fc.webp',
            aspect: 'aspect-[16/11]',
            offsetClass: 'lg:translate-y-8',
            floatY: '-4px',
            rotate: '-0.3deg',
        },
        {
            num: '06',
            title: 'FOOTBALL',
            image: '/images/club/floodlit-match-action-topgrade-london-fc.webp',
            aspect: 'aspect-[16/11]',
            offsetClass: 'lg:translate-y-20',
            floatY: '-5px',
            rotate: '0.25deg',
        },
    ];
});

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
let motionMedia: MediaQueryList | null = null;

const onMotionChange = (e: MediaQueryListEvent) => {
    prefersReducedMotion.value = e.matches;
};

onMounted(async () => {
    await nextTick();

    motionMedia = window.matchMedia('(prefers-reduced-motion: reduce)');
    prefersReducedMotion.value = motionMedia.matches;
    motionMedia.addEventListener('change', onMotionChange);

    const reduceMotion = prefersReducedMotion.value;
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
        { threshold: 0.1, rootMargin: '0px 0px -4% 0px' },
    );

    document.querySelectorAll('[data-reveal]').forEach((el) => io?.observe(el));

    if (heroVideoRef.value && !prefersReducedMotion.value) {
        heroVideoRef.value.muted = true;
        heroVideoRef.value.play().catch(() => {
            videoFailed.value = true;
        });
    }

    if (reduceMotion || !ball) {
        if (ball) {
            ball.style.opacity = '0';
        }

        root.style.setProperty('--zoom', '1');

        return;
    }

    /*
     * Ball Waypoints (Atmospheric Visual Thread):
     * Ball starts dormant (opacity: 0, visibility: hidden) while the 3D Crest is showcased on initial hero load.
     * On downward scroll, crest transitions out and ball materializes (at: 0.50), rotating
     * and routing through negative space across subsequent sections.
     * Mobile waypoints (m) keep the ball considerably smaller and unobtrusive.
     */
    const STOPS = [
        {
            sec: 'hero',
            at: 0.0,
            x: 28,
            y: 0,
            s: 0.5,
            r: 0,
            o: 0.0,
            p: 0.16,
            m: { x: 0, y: 20, s: 0.32, o: 0.0 },
        },
        {
            sec: 'hero',
            at: 0.5,
            x: 26,
            y: 4,
            s: 0.65,
            r: 90,
            o: 0.85,
            p: 0.16,
            m: { x: 0, y: 20, s: 0.38, o: 0.85 },
        },
        {
            sec: 'hero',
            at: 0.9,
            x: -28,
            y: 16,
            s: 0.45,
            r: 180,
            o: 0.7,
            p: 0.1,
            m: { x: -18, y: 18, s: 0.26, o: 0.65 },
        },
        {
            sec: 'develop',
            at: 0.5,
            x: -38,
            y: 8,
            s: 0.42,
            r: 250,
            o: 0.65,
            p: 0.07,
            m: { x: -24, y: 12, s: 0.24, o: 0.6 },
        },
        {
            sec: 'creed',
            at: 0.5,
            x: 0,
            y: 0,
            s: 2.6,
            r: 430,
            o: 0.96,
            p: 0.16,
            m: { s: 1.85, o: 0.9 },
        },
        {
            sec: 'train',
            at: 0.35,
            x: 36,
            y: -8,
            s: 0.4,
            r: 580,
            o: 0.65,
            p: 0.05,
            m: { x: 24, y: -10, s: 0.22, o: 0.6 },
        },
        {
            sec: 'teams',
            at: 0.5,
            x: -34,
            y: 0,
            s: 0.36,
            r: 690,
            o: 0.5,
            p: 0.07,
            m: { x: -22, y: -6, s: 0.2, o: 0.45 },
        },
        {
            sec: 'gallery',
            at: 0.45,
            x: 32,
            y: 4,
            s: 0.36,
            r: 780,
            o: 0.5,
            p: 0.07,
            m: { x: 22, y: 6, s: 0.22, o: 0.45 },
        },
        {
            sec: 'matchday',
            at: 0.5,
            x: -32,
            y: 14,
            s: 0.38,
            r: 870,
            o: 0.6,
            p: 0.05,
            m: { x: -20, y: 14, s: 0.22, o: 0.55 },
        },
        {
            sec: 'cta',
            at: 0.5,
            x: 0,
            y: 18,
            s: 0.55,
            r: 960,
            o: 0.55,
            p: 0.1,
            m: { y: 16, s: 0.32, o: 0.5 },
        },
        {
            sec: 'footer',
            at: 0.5,
            x: 22,
            y: 8,
            s: 0.9,
            r: 1080,
            o: 0.7,
            p: 0.12,
            m: { x: 0, y: 12, s: 0.55, o: 0.65 },
        },
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

            if (!el) {
                return null;
            }

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
                if (k.m.x !== undefined) {
                    o.x = k.m.x;
                }

                if (k.m.y !== undefined) {
                    o.ty = k.m.y;
                }

                if (k.m.s !== undefined) {
                    o.s = k.m.s;
                }

                if (k.m.o !== undefined) {
                    o.o = k.m.o;
                }
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

    const pars = Array.from(
        document.querySelectorAll<HTMLElement>('[data-par]'),
    );

    const animate = (time: number) => {
        // Damped interpolation loop without scroll hijacking
        currentScrollY += (targetScrollY - currentScrollY) * 0.075;
        const sy = currentScrollY + window.innerHeight * 0.5;

        // Hero 3D Crest transition to football visual object on initial downward scroll (0px -> 240px)
        const heroScrollProgress = Math.min(
            1,
            Math.max(0, currentScrollY / 240),
        );
        const crestElements = document.querySelectorAll<HTMLElement>(
            '#hero-3d-crest, .hero-3d-crest-mobile',
        );

        if (crestElements.length > 0 && !reduceMotion) {
            const crestOpacity = Math.max(0, 1 - heroScrollProgress);
            const crestScale = Math.max(0.72, 1 - heroScrollProgress * 0.28);
            const crestY = currentScrollY * 0.12;

            crestElements.forEach((el) => {
                el.style.opacity = crestOpacity.toFixed(3);
                el.style.transform = `translate3d(0, ${crestY.toFixed(1)}px, 0) scale(${crestScale.toFixed(3)})`;
                el.style.pointerEvents = crestOpacity <= 0.05 ? 'none' : 'auto';
                el.style.visibility =
                    crestOpacity <= 0.001 ? 'hidden' : 'visible';
            });
        }

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

            // Ball starts dormant (opacity: 0, hidden) at scroll 0, and smoothly emerges as the crest fades on downward scroll
            if (currentScrollY <= 0.5) {
                ball.style.opacity = '0';
                ball.style.visibility = 'hidden';
            } else {
                ball.style.visibility = 'visible';
                const finalBallOpacity = op * heroScrollProgress;
                ball.style.opacity = finalBallOpacity.toFixed(3);
            }

            ball.style.transform = `translate3d(calc(-50% + ${x.toFixed(2)}vw), calc(-50% + ${finalY.toFixed(2)}vh), 0) scale(${s.toFixed(3)}) rotate(${finalRot.toFixed(1)}deg)`;

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

    if (motionMedia) {
        motionMedia.removeEventListener('change', onMotionChange);
    }
});
</script>

<template>
    <SeoHead
        title="TopGrade London FC | Youth Football Club in London"
        description="TopGrade London FC is a youth football club based in Tottenham, London, with teams, training and matchday football for young players."
        path="/"
    />
    <JsonLd :schema="sportsClubSchema" />

    <div
        class="bg-tg-bg text-tg-text selection:bg-tg-accent relative overflow-hidden selection:text-white"
    >
        <!-- Creed Zoom Dimming Backdrop (z-index: 4, below ball z-index 5) -->
        <div
            id="creed-backdrop"
            class="creed-backdrop pointer-events-none fixed inset-0 transition-opacity duration-200"
            style="background: rgba(8, 4, 15, 0.94); z-index: 4; opacity: 0"
            aria-hidden="true"
        />

        <!-- Official Match Ball Physics Layer (z-index: 5, above pitch grid z-index 2, behind section surfaces z-index 10) -->
        <div
            id="ball"
            class="ball-layer pointer-events-none fixed left-1/2 top-1/2 will-change-transform"
            style="z-index: 5; opacity: 0"
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

        <!-- 00 — OFFICIAL CLUB STATUS BAR (z-index: 10) -->
        <div
            class="border-tg-border text-tg-text-muted relative z-10 border-b px-4 py-2.5 text-xs sm:px-6 lg:px-8"
            style="background: var(--surface-dense)"
        >
            <div
                class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3"
            >
                <div class="flex items-center gap-2.5">
                    <span
                        class="inline-block h-2 w-2 rounded-full"
                        style="background: var(--tg-accent)"
                    />
                    <span
                        class="text-tg-text-strong font-bold uppercase tracking-wider"
                        >TopGrade London FC</span
                    >
                    <span class="text-tg-border hidden sm:inline">•</span>
                    <span class="hidden sm:inline"
                        >North &amp; East London Youth Football Club</span
                    >
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <span class="text-tg-text-strong"
                        >Youth Football · U7–U16</span
                    >
                    <span class="text-tg-border">•</span>
                    <span>Tottenham &amp; Hackney Grounds</span>
                    <span class="text-tg-border">•</span>
                    <span>Community Interest Company (CIC)</span>
                </div>
            </div>
        </div>

        <!-- 01 — HERO SECTION: Left-Aligned Architecture with 3D Decorative Crest & Metamorphosis to Ball -->
        <section
            id="hero"
            class="relative flex min-h-[92vh] items-center justify-start overflow-hidden px-4 pb-16 pt-20 sm:min-h-[105vh] sm:px-6 sm:pb-24 sm:pt-28 lg:px-8"
            style="background: transparent"
        >
            <!-- Atmospheric Hero Media Layer (Looping Football Video with Fallback Poster) -->
            <div
                v-if="
                    (heroVideoUrl && !videoFailed && !prefersReducedMotion) ||
                    heroPosterUrl
                "
                class="pointer-events-none absolute inset-0 h-full w-full select-none overflow-hidden"
                style="z-index: 1"
                aria-hidden="true"
            >
                <video
                    v-if="heroVideoUrl && !videoFailed && !prefersReducedMotion"
                    ref="heroVideoRef"
                    class="h-full w-full object-cover object-center transition-opacity duration-700"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="metadata"
                    :poster="heroPosterUrl || undefined"
                    @error="onVideoError"
                >
                    <source :src="heroVideoUrl" type="video/mp4" />
                </video>
                <img
                    v-else-if="heroPosterUrl"
                    :src="heroPosterUrl"
                    alt=""
                    class="h-full w-full object-cover object-center transition-opacity duration-700"
                />
                <!-- Subtle flat translucent overlay (restrained 35%, no gradients, no glows) -->
                <div class="absolute inset-0 bg-[#08040f]/35" />
            </div>

            <!-- Content Grid: Left-Aligned Copy & Primary CTAs, Right-Aligned 3D Decorative Crest -->
            <div
                class="relative z-20 mx-auto grid w-full max-w-7xl grid-cols-1 items-center gap-8 lg:grid-cols-12 lg:gap-12"
            >
                <!-- Left Column: Branding, Typography, CTAs, Mobile 3D Crest, and Facts -->
                <div
                    class="flex flex-col items-start text-left lg:col-span-7 xl:col-span-7"
                >
                    <!-- Clean Kicker (Single primary identity treatment: no redundant official logo above headline) -->
                    <div
                        class="animate-hero-drop mb-4 inline-flex items-center gap-2 sm:mb-6"
                    >
                        <span
                            class="h-2 w-2 rounded-full"
                            style="background: var(--tg-accent)"
                        />
                        <span
                            class="text-tg-accent text-xs font-black uppercase tracking-widest sm:text-sm"
                        >
                            {{ heroEyebrow }}
                        </span>
                        <span class="text-tg-border text-xs">•</span>
                        <span
                            class="text-tg-text-muted text-[10px] font-semibold uppercase tracking-wider sm:text-xs"
                        >
                            Est. 2022 · London
                        </span>
                    </div>

                    <!-- Monumental Headline (Left-Aligned) -->
                    <h1
                        class="text-tg-text-strong mb-4 font-normal uppercase tracking-tight sm:mb-6"
                        style="
                            font-family: var(--tg-display);
                            font-size: clamp(2.6rem, 7.2vw, 6.2rem);
                            line-height: 0.9;
                            text-shadow: 0 4px 24px rgba(0, 0, 0, 0.45);
                        "
                    >
                        <template v-if="heroHeadlineParts.length >= 3">
                            <span class="animate-hero-rise inline-block">{{
                                heroHeadlineParts[0]
                            }}</span
                            ><br />
                            <span
                                class="animate-hero-rise text-tg-accent inline-block"
                                style="animation-delay: 0.12s"
                                >{{ heroHeadlineParts[1] }}</span
                            ><br />
                            <span
                                class="animate-hero-rise inline-block"
                                style="animation-delay: 0.24s"
                                >{{
                                    heroHeadlineParts.slice(2).join(' ')
                                }}</span
                            >
                        </template>
                        <template v-else>
                            <span class="animate-hero-rise inline-block">{{
                                heroHeadline
                            }}</span>
                        </template>
                    </h1>

                    <!-- Subheadline (Left-Aligned) -->
                    <p
                        class="text-tg-text animate-hero-fade mb-8 max-w-xl text-sm font-normal leading-relaxed sm:mb-10 sm:text-base md:text-lg"
                        style="text-shadow: 0 2px 12px rgba(0, 0, 0, 0.4)"
                    >
                        {{ heroDescription }}
                    </p>

                    <!-- CTA Buttons (Left-Aligned) -->
                    <div
                        class="animate-hero-fade relative z-20 mb-8 flex w-full flex-col justify-start gap-3 sm:mb-10 sm:w-auto sm:flex-row sm:gap-4"
                        style="animation-delay: 0.4s"
                    >
                        <Link
                            :href="heroPrimaryCta.url"
                            class="tg-btn tg-focus justify-center shadow-sm"
                        >
                            <span>{{ heroPrimaryCta.label }}</span>
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <a
                            href="#train"
                            class="tg-btn ghost tg-focus justify-center"
                        >
                            <span>Training Schedule</span>
                        </a>
                    </div>

                    <!-- 3D Decorative Crest on Mobile (Placed cleanly below CTA, floats subtly, transitions out on scroll) -->
                    <div
                        class="pointer-events-none my-4 mb-8 flex w-full select-none flex-col items-center justify-center lg:hidden"
                    >
                        <div class="hero-3d-crest-mobile will-change-transform">
                            <div class="animate-crest-float">
                                <picture>
                                    <source
                                        srcset="/images/topgrade-crest-3d.webp"
                                        type="image/webp"
                                    />
                                    <img
                                        src="/images/topgrade-crest-3d.png"
                                        alt="TopGrade 3D Crest"
                                        class="h-auto w-[48vw] max-w-[200px] drop-shadow-2xl"
                                    />
                                </picture>
                            </div>
                        </div>
                    </div>

                    <!-- Facts Bar -->
                    <div
                        class="border-tg-border grid w-full max-w-xl grid-cols-2 gap-3 border-t pt-6 sm:grid-cols-4 sm:pt-8"
                    >
                        <div
                            v-for="stat in quickStats"
                            :key="stat.label"
                            class="bg-tg-bg-deep/80 border-tg-border rounded border p-3 text-left sm:p-3.5"
                        >
                            <div
                                class="text-tg-text-strong text-base font-normal uppercase sm:text-xl"
                                style="font-family: var(--tg-display)"
                            >
                                {{ stat.value }}
                            </div>
                            <div
                                class="text-tg-accent mt-0.5 text-[10px] font-bold uppercase tracking-wider sm:text-[11px]"
                            >
                                {{ stat.label }}
                            </div>
                            <div
                                class="text-tg-text-muted mt-0.5 text-[9px] sm:text-[10px]"
                            >
                                {{ stat.detail }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: 3D Decorative Crest on Desktop (Initial Hero Visual, transitions out on scroll) -->
                <div
                    class="pointer-events-none relative my-6 hidden select-none items-center justify-end lg:col-span-5 lg:my-0 lg:flex xl:col-span-5"
                >
                    <div
                        id="hero-3d-crest"
                        class="relative will-change-transform"
                    >
                        <div class="animate-crest-float">
                            <picture>
                                <source
                                    srcset="/images/topgrade-crest-3d.webp"
                                    type="image/webp"
                                />
                                <img
                                    src="/images/topgrade-crest-3d.png"
                                    alt="TopGrade 3D Crest"
                                    class="h-auto w-[32vw] max-w-[420px] drop-shadow-2xl"
                                />
                            </picture>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scroll Cue -->
            <div
                class="text-tg-text-muted absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 select-none flex-col items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest sm:bottom-6"
            >
                <span
                    class="h-5 w-[1.5px] animate-pulse sm:h-6"
                    style="background: var(--tg-accent)"
                />
                <span>Scroll</span>
            </div>
        </section>

        <!-- 02 — MARQUEE TICKER: Energetic Club Identity -->
        <div
            class="border-tg-border relative z-10 overflow-hidden border-y py-3"
            style="background: rgba(42, 11, 87, 0.85)"
            aria-hidden="true"
        >
            <div
                class="marquee-track flex gap-12 whitespace-nowrap font-normal text-white"
                style="
                    font-family: var(--tg-display);
                    font-size: 1.35rem;
                    letter-spacing: 0.05em;
                "
            >
                <div class="animate-marquee flex shrink-0 items-center gap-12">
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
            class="border-tg-border relative z-10 border-b px-4 py-20 sm:px-6 sm:py-32 lg:px-8"
            style="background: var(--surface-section)"
        >
            <div class="mx-auto max-w-7xl">
                <div class="grid items-center gap-10 lg:grid-cols-12 lg:gap-16">
                    <!-- Left: Philosophy and 4 Pillars -->
                    <div class="lg:col-span-7">
                        <p class="tg-eyebrow" data-reveal>What We Do</p>
                        <h2
                            data-reveal
                            style="
                                --d: 60ms;
                                font-family: var(--tg-display);
                                font-size: clamp(2.4rem, 6vw, 4.8rem);
                            "
                            class="text-tg-text-strong mb-4"
                        >
                            Develop<br />Your Game
                        </h2>
                        <p
                            data-reveal
                            style="--d: 110ms"
                            class="text-tg-text mb-8 max-w-xl text-base sm:text-lg"
                        >
                            Every training session is coached around four core
                            fundamentals. Players work on all of them, across
                            every age group, every week.
                        </p>

                        <!-- Pillars List -->
                        <div class="space-y-0">
                            <div
                                v-for="(pillar, idx) in pillars"
                                :key="pillar.num"
                                data-reveal
                                :style="{ '--d': `${140 + idx * 50}ms` }"
                                class="border-tg-border flex items-start gap-4 border-t py-4"
                            >
                                <span
                                    class="text-tg-accent min-w-[2.4rem] pt-0.5 text-xl font-normal sm:text-2xl"
                                    style="font-family: var(--tg-display)"
                                >
                                    {{ pillar.num }}
                                </span>
                                <div>
                                    <h3
                                        class="text-tg-text-strong text-lg font-normal uppercase sm:text-xl"
                                        style="font-family: var(--tg-display)"
                                    >
                                        {{ pillar.title }}
                                    </h3>
                                    <p class="text-tg-text-muted mt-1 text-sm">
                                        {{ pillar.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Training Pitch Action Photography -->
                    <div
                        class="lg:col-span-5"
                        data-reveal="right"
                        style="--d: 120ms"
                    >
                        <div
                            class="border-tg-border bg-tg-bg-deep group relative w-full cursor-pointer overflow-hidden rounded-xl border shadow-xl transition-all duration-300 hover:border-tg-accent/70"
                            role="button"
                            tabindex="0"
                            aria-label="View full photo of youth player match action"
                            @click="
                                openImagePreview({
                                    url: '/images/club/player-match-action-topgrade-london-fc.webp',
                                    alt: 'TopGrade London FC youth player in purple kit competing for possession during a league match',
                                    title: 'Player Match Action · Ball Possession',
                                    subtitle:
                                        'TopGrade youth player demonstrating ball mastery and positional composure under league matchday pressure.',
                                })
                            "
                            @keydown.enter="
                                openImagePreview({
                                    url: '/images/club/player-match-action-topgrade-london-fc.webp',
                                    alt: 'TopGrade London FC youth player in purple kit competing for possession during a league match',
                                    title: 'Player Match Action · Ball Possession',
                                    subtitle:
                                        'TopGrade youth player demonstrating ball mastery and positional composure under league matchday pressure.',
                                })
                            "
                        >
                            <img
                                src="/images/club/player-match-action-topgrade-london-fc.webp"
                                alt="TopGrade London FC youth player in purple kit competing for possession during a league match"
                                class="block h-auto w-full select-none"
                                loading="lazy"
                            />
                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                            />
                            <div
                                class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-xs text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                            >
                                <span class="text-tg-accent font-medium"
                                    >Match Action · Ball Mastery</span
                                >
                            </div>
                            <span
                                class="absolute right-3 top-3 flex items-center gap-1.5 rounded-full border border-white/20 bg-black/75 px-3 py-1.5 text-xs text-white shadow-lg backdrop-blur-sm transition-all duration-200 group-hover:border-tg-accent group-hover:bg-black/90"
                            >
                                <Maximize2 class="text-tg-accent size-3.5" />
                                <span
                                    class="text-[11px] font-medium uppercase tracking-wider"
                                    >Full View</span
                                >
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 04 — CREED SECTION: Dominant Monumental Payoff -->
        <section
            id="creed"
            class="relative z-10 flex min-h-[90vh] flex-col items-center justify-center px-4 text-center sm:min-h-[100vh]"
            style="background: transparent"
        >
            <div class="relative z-20 mx-auto max-w-3xl">
                <h2
                    class="text-tg-text-strong mb-4 font-normal uppercase tracking-tight"
                    style="
                        font-family: var(--tg-display);
                        font-size: clamp(2.8rem, 9vw, 7.5rem);
                        line-height: 0.9;
                    "
                >
                    Always the best
                </h2>
                <p
                    class="text-tg-highlight text-xs font-semibold uppercase tracking-[0.2em] sm:text-lg"
                >
                    Training · Teamwork · Playing Experience
                </p>
            </div>
        </section>

        <!-- 05 — TRAIN WITH US: Editorial Club Timetable + Available Sessions -->
        <section
            id="train"
            class="border-tg-border relative z-10 border-y px-4 py-20 sm:px-6 sm:py-32 lg:px-8"
            style="background: var(--surface-dense)"
        >
            <div class="mx-auto max-w-7xl">
                <div
                    class="border-tg-border mb-12 flex flex-col justify-between gap-6 border-b pb-6 md:flex-row md:items-end"
                >
                    <div>
                        <p class="tg-eyebrow" data-reveal>
                            Where &amp; When We Train
                        </p>
                        <h2
                            data-reveal
                            style="
                                --d: 60ms;
                                font-family: var(--tg-display);
                                font-size: clamp(2.4rem, 6vw, 4.5rem);
                            "
                            class="text-tg-text-strong"
                        >
                            Train With Us
                        </h2>
                    </div>
                    <p
                        data-reveal
                        style="--d: 100ms"
                        class="text-tg-text-muted max-w-md text-sm"
                    >
                        Weekly club training in Tottenham for youth players aged
                        U7 to U16. Bring boots, shin guards, and water.
                    </p>
                </div>

                <!-- Club Training Timetable (Editorial Presentation) -->
                <div class="mb-16 grid gap-6 sm:gap-10 md:grid-cols-2">
                    <div
                        v-for="(sched, idx) in trainingSchedule"
                        :key="sched.days"
                        data-reveal
                        :style="{ '--d': `${120 + idx * 80}ms` }"
                        class="bg-tg-bg-deep/80 border-tg-border flex flex-col justify-between space-y-6 rounded-sm border p-6 sm:p-8"
                    >
                        <div class="space-y-6">
                            <!-- Day & Badge -->
                            <div
                                class="border-tg-border flex items-center justify-between border-b pb-4"
                            >
                                <h3
                                    class="text-tg-text-strong text-2xl font-normal uppercase sm:text-3xl"
                                    style="font-family: var(--tg-display)"
                                >
                                    {{ sched.days }}
                                </h3>
                                <span
                                    class="rounded-xs bg-tg-bg border-tg-border text-tg-accent border px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider"
                                >
                                    {{ sched.badge }}
                                </span>
                            </div>

                            <!-- Sessions & Times (Clean, scannable timetable) -->
                            <div class="space-y-2.5">
                                <div
                                    v-for="(session, sIdx) in sched.sessions"
                                    :key="sIdx"
                                    class="border-tg-border/60 flex items-center justify-between border-b py-2"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <span
                                            class="text-tg-text-strong text-base font-bold sm:text-lg"
                                            >{{ session.age }}</span
                                        >
                                    </div>
                                    <span
                                        class="text-tg-accent text-sm font-semibold sm:text-base"
                                        >{{ session.time }}</span
                                    >
                                </div>
                            </div>

                            <!-- Venue Info -->
                            <div class="space-y-1.5 pt-2">
                                <div
                                    class="text-tg-text-strong text-base font-bold"
                                >
                                    {{ sched.venue.name }}
                                </div>
                                <div class="text-tg-accent text-xs font-medium">
                                    {{ sched.venue.facility }}
                                </div>
                                <div
                                    class="text-tg-text-muted flex items-start gap-1.5 text-xs"
                                >
                                    <MapPin
                                        class="text-tg-border-strong mt-0.5 h-3.5 w-3.5 shrink-0"
                                    />
                                    <span>{{ sched.venue.address }}</span>
                                </div>
                                <div
                                    class="text-tg-text-muted pt-0.5 text-[11px]"
                                >
                                    <span class="text-tg-text font-semibold"
                                        >Surface:</span
                                    >
                                    {{ sched.venue.surface }}
                                </div>
                            </div>
                        </div>

                        <!-- Action -->
                        <div class="border-tg-border border-t pt-4">
                            <Link
                                href="/bookings/free-trial-session"
                                class="tg-link text-tg-accent flex items-center justify-between text-sm"
                            >
                                <span>Book an Introductory Trial</span>
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Contextual Bookable Sessions (If active in database) -->
                <div
                    v-if="featuredActivities && featuredActivities.length > 0"
                    class="border-tg-border mt-8 border-t pt-10"
                >
                    <div
                        class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"
                    >
                        <div>
                            <span
                                class="text-tg-accent mb-1 block text-xs font-bold uppercase tracking-widest"
                            >
                                Join a Session
                            </span>
                            <h3
                                class="text-tg-text-strong text-2xl font-normal uppercase sm:text-3xl"
                                style="font-family: var(--tg-display)"
                            >
                                Available Sessions
                            </h3>
                        </div>
                        <Link
                            href="/bookings"
                            class="tg-link text-tg-accent flex items-center gap-1.5 text-xs"
                        >
                            <span>View All Sessions</span>
                            <ArrowRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="item in featuredActivities"
                            :key="item.id"
                            data-reveal
                            class="bg-tg-bg-deep/80 border-tg-border hover:border-tg-border-strong flex flex-col justify-between rounded-sm border p-6 transition-all"
                        >
                            <div>
                                <h4
                                    class="text-tg-text-strong mb-2 text-xl font-normal uppercase"
                                    style="font-family: var(--tg-display)"
                                >
                                    {{ item.name }}
                                </h4>
                                <p
                                    class="text-tg-text-muted mb-6 line-clamp-3 text-xs leading-relaxed"
                                >
                                    {{ item.description }}
                                </p>
                            </div>

                            <div class="border-tg-border border-t pt-4">
                                <Link
                                    :href="`/bookings/${item.slug}`"
                                    class="tg-btn ghost w-full justify-center text-xs"
                                >
                                    <span>{{
                                        item.slug.includes('trial')
                                            ? 'Book a Trial'
                                            : 'Book Session'
                                    }}</span>
                                    <ArrowRight class="h-3.5 w-3.5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wide Training Pitch Photography -->
                <div class="mt-14" data-reveal style="--d: 200ms">
                    <div
                        class="border-tg-border bg-tg-bg-deep group relative w-full cursor-pointer overflow-hidden rounded-xl border shadow-xl transition-all duration-300 hover:border-tg-accent/70"
                        role="button"
                        tabindex="0"
                        aria-label="View full photo of floodlit training pitch"
                        @click="
                            openImagePreview({
                                url: '/images/club/floodlit-training-pitch-topgrade-london-fc.webp',
                                alt: 'TopGrade London FC all-weather training pitch illuminated under evening floodlights',
                                title: 'Training Facility · All-Weather Floodlit Pitch',
                                subtitle:
                                    'High-standard evening training environment illuminated under floodlights.',
                            })
                        "
                        @keydown.enter="
                            openImagePreview({
                                url: '/images/club/floodlit-training-pitch-topgrade-london-fc.webp',
                                alt: 'TopGrade London FC all-weather training pitch illuminated under evening floodlights',
                                title: 'Training Facility · All-Weather Floodlit Pitch',
                                subtitle:
                                    'High-standard evening training environment illuminated under floodlights.',
                            })
                        "
                    >
                        <img
                            src="/images/club/floodlit-training-pitch-topgrade-london-fc.webp"
                            alt="TopGrade London FC all-weather training pitch illuminated under evening floodlights"
                            class="block h-auto w-full select-none"
                            loading="lazy"
                        />
                        <span
                            class="absolute right-3 top-3 flex items-center gap-1.5 rounded-full border border-white/20 bg-black/75 px-3 py-1.5 text-xs text-white shadow-lg backdrop-blur-sm transition-all duration-200 group-hover:border-tg-accent group-hover:bg-black/90"
                        >
                            <Maximize2 class="text-tg-accent size-3.5" />
                            <span
                                class="text-[11px] font-medium uppercase tracking-wider"
                                >Full View</span
                            >
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 06 — OUR TEAMS: Asymmetric Editorial Squad Photography (Informational Club Entities) -->
        <section
            id="teams"
            class="border-tg-border relative z-10 border-b px-4 py-20 sm:px-6 sm:py-32 lg:px-8"
            style="background: var(--surface-section)"
        >
            <div class="mx-auto max-w-7xl">
                <!-- Section Header -->
                <div
                    class="border-tg-border mb-16 flex flex-col justify-between gap-6 border-b pb-6 md:flex-row md:items-end"
                >
                    <div>
                        <p class="tg-eyebrow" data-reveal>Our Teams</p>
                        <h2
                            data-reveal
                            style="
                                --d: 60ms;
                                font-family: var(--tg-display);
                                font-size: clamp(2.4rem, 6vw, 4.5rem);
                            "
                            class="text-tg-text-strong"
                        >
                            Built Through The Game.
                        </h2>
                    </div>
                    <p
                        data-reveal
                        style="--d: 100ms"
                        class="text-tg-text-muted max-w-md text-sm"
                    >
                        Structured progression from Foundation Phase through to
                        competitive London youth league football.
                    </p>
                </div>

                <!-- True Asymmetric Editorial Visual Rhythm -->
                <div
                    class="grid grid-cols-1 items-start gap-8 md:grid-cols-12 lg:gap-12"
                >
                    <div
                        v-for="(squad, idx) in squads"
                        :key="squad.name"
                        :class="[
                            squad.layoutClass,
                            'group flex flex-col space-y-3',
                        ]"
                        data-reveal
                        :style="{ '--d': `${80 + idx * 50}ms` }"
                    >
                        <!-- Varied Aspect Ratio Photo Container with Float Hover -->
                        <div
                            :class="[
                                squad.aspectClass,
                                'tg-float-card bg-tg-bg-deep border-tg-border relative w-full cursor-pointer overflow-hidden rounded-sm border shadow-sm',
                            ]"
                            :style="{
                                '--float-y': squad.floatY,
                                '--rotate': squad.rotate,
                            }"
                            role="button"
                            tabindex="0"
                            :aria-label="`View full photo of ${squad.name} Squad`"
                            @click="
                                openImagePreview({
                                    url: squad.image,
                                    alt: `TopGrade London FC ${squad.name} Squad`,
                                    title: `${squad.name} · ${squad.stage}`,
                                    subtitle: squad.description,
                                })
                            "
                            @keydown.enter="
                                openImagePreview({
                                    url: squad.image,
                                    alt: `TopGrade London FC ${squad.name} Squad`,
                                    title: `${squad.name} · ${squad.stage}`,
                                    subtitle: squad.description,
                                })
                            "
                        >
                            <img
                                :src="squad.image"
                                :alt="`TopGrade London FC ${squad.name} Squad`"
                                :class="[
                                    'tg-float-img h-full w-full object-cover',
                                    squad.cropPosition,
                                ]"
                            />
                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"
                            />

                            <!-- Top-right Expand Button -->
                            <span
                                class="absolute right-3 top-3 flex items-center gap-1 rounded-full border border-white/20 bg-black/75 px-2.5 py-1 text-[10px] text-white opacity-0 backdrop-blur-sm transition-opacity duration-200 group-hover:opacity-100"
                            >
                                <Maximize2 class="text-tg-accent size-3" />
                                <span class="hidden sm:inline">Full Photo</span>
                            </span>

                            <!-- Bottom Tag inside Image -->
                            <div
                                class="pointer-events-none absolute bottom-4 left-4 right-4 flex items-end justify-between"
                            >
                                <div>
                                    <span
                                        class="text-tg-accent mb-0.5 block text-[10px] font-bold uppercase tracking-widest"
                                    >
                                        {{ squad.stage }}
                                    </span>
                                    <h3
                                        class="text-3xl font-normal uppercase leading-none text-white sm:text-4xl"
                                        style="font-family: var(--tg-display)"
                                    >
                                        {{ squad.name }}
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <!-- Supporting Editorial Copy Beneath -->
                        <p
                            class="text-tg-text-muted px-1 text-xs leading-relaxed"
                        >
                            {{ squad.description }}
                        </p>
                    </div>
                </div>

                <!-- Single Section-Level Contextual CTA -->
                <div
                    class="border-tg-border mt-16 flex flex-col items-center justify-between gap-4 border-t pt-8 sm:flex-row"
                >
                    <div
                        class="text-tg-text-muted flex items-center gap-2.5 text-xs"
                    >
                        <CheckCircle2 class="text-tg-accent h-4 w-4 shrink-0" />
                        <span
                            >Competitive youth squads participating in local
                            sanctioned youth football leagues.</span
                        >
                    </div>

                    <a
                        href="#train"
                        class="tg-link text-tg-accent flex items-center gap-2 text-sm"
                    >
                        <span>Looking to join TopGrade? Find a session</span>
                        <ArrowRight class="h-4 w-4" />
                    </a>
                </div>
            </div>
        </section>

        <!-- 07 — MOMENTS: Endless Photographic Ribbon -->
        <section
            id="gallery"
            class="border-tg-border relative z-10 border-b px-4 py-20 sm:px-6 sm:py-32 lg:px-8"
            style="background: var(--surface-section)"
        >
            <div class="mx-auto max-w-7xl">
                <div
                    class="border-tg-border mb-16 flex flex-col justify-between gap-6 border-b pb-6 md:flex-row md:items-end"
                >
                    <div>
                        <p class="tg-eyebrow" data-reveal>The Game</p>
                        <h2
                            data-reveal
                            style="
                                --d: 60ms;
                                font-family: var(--tg-display);
                                font-size: clamp(2.4rem, 6vw, 4.5rem);
                            "
                            class="text-tg-text-strong"
                        >
                            Moments
                        </h2>
                    </div>
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center"
                    >
                        <p
                            data-reveal
                            style="--d: 100ms"
                            class="text-tg-text-muted max-w-md text-sm"
                        >
                            Training. Matchday. The moments in between.
                        </p>
                        <Link
                            href="/moments"
                            class="tg-btn ghost inline-flex shrink-0 items-center gap-1.5 self-start px-3.5 py-1.5 text-xs shadow-sm sm:self-auto"
                        >
                            <span>Explore Stories</span>
                            <ArrowRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>
                </div>

                <!-- Endless Photographic Ribbon (Offset stream, minimal typography) -->
                <div
                    class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 lg:gap-10"
                >
                    <div
                        v-for="(moment, mIdx) in momentsRibbon"
                        :key="moment.num"
                        :class="[
                            'group flex flex-col space-y-2.5 transition-transform duration-300',
                            moment.offsetClass,
                        ]"
                        data-reveal
                        :style="{ '--d': `${100 + mIdx * 60}ms` }"
                    >
                        <!-- Vertical Card Image with Float Hover -->
                        <div
                            :class="[
                                moment.aspect,
                                'tg-float-card bg-tg-bg-deep border-tg-border relative w-full cursor-pointer overflow-hidden rounded-sm border shadow-md',
                            ]"
                            :style="{
                                '--float-y': moment.floatY,
                                '--rotate': moment.rotate,
                            }"
                            role="button"
                            tabindex="0"
                            :aria-label="`View full photo of moment ${moment.num}: ${moment.title}`"
                            @click="
                                openImagePreview({
                                    url: moment.image,
                                    alt: moment.title,
                                    title: `Moment ${moment.num} · ${moment.title}`,
                                    subtitle:
                                        'TopGrade London FC visual archive.',
                                })
                            "
                            @keydown.enter="
                                openImagePreview({
                                    url: moment.image,
                                    alt: moment.title,
                                    title: `Moment ${moment.num} · ${moment.title}`,
                                    subtitle:
                                        'TopGrade London FC visual archive.',
                                })
                            "
                        >
                            <img
                                :src="moment.image"
                                :alt="moment.title"
                                class="tg-float-img h-full w-full object-cover"
                            />
                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent"
                            />

                            <span
                                class="absolute right-2.5 top-2.5 flex items-center gap-1 rounded-full border border-white/20 bg-black/75 px-2 py-0.5 text-[9px] text-white opacity-0 backdrop-blur-sm transition-opacity duration-200 group-hover:opacity-100"
                            >
                                <Maximize2 class="text-tg-accent size-2.5" />
                                <span class="hidden sm:inline">View</span>
                            </span>

                            <div
                                class="pointer-events-none absolute bottom-4 left-4 right-4 flex items-center justify-between"
                            >
                                <span
                                    class="text-lg font-normal uppercase tracking-wider text-white sm:text-xl"
                                    style="font-family: var(--tg-display)"
                                >
                                    {{ moment.title }}
                                </span>
                                <span
                                    class="text-tg-accent font-mono text-xs font-bold"
                                    >{{ moment.num }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 08 — MATCHDAY: Home Venues -->
        <section
            id="matchday"
            class="border-tg-border relative z-10 border-b px-4 py-20 sm:px-6 sm:py-32 lg:px-8"
            style="background: var(--surface-dense)"
        >
            <div class="mx-auto max-w-7xl">
                <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-16">
                    <!-- Left Column: Editorial Venue List -->
                    <div class="space-y-8 lg:col-span-6">
                        <div>
                            <p class="tg-eyebrow" data-reveal>Matchday</p>
                            <h2
                                data-reveal
                                style="
                                    --d: 60ms;
                                    font-family: var(--tg-display);
                                    font-size: clamp(2.4rem, 6vw, 4.5rem);
                                "
                                class="text-tg-text-strong mb-4"
                            >
                                Home Venues
                            </h2>
                            <p
                                data-reveal
                                style="--d: 100ms"
                                class="text-tg-text-muted max-w-md text-sm"
                            >
                                Official home grounds and matchday facilities
                                across North and East London.
                            </p>
                        </div>

                        <!-- Numbered Home Venues -->
                        <div class="space-y-0">
                            <div
                                v-for="(venue, vIdx) in homeVenues"
                                :key="venue.num"
                                data-reveal
                                :style="{ '--d': `${140 + vIdx * 50}ms` }"
                                class="border-tg-border space-y-1 border-t py-5"
                            >
                                <div
                                    class="flex items-baseline justify-between gap-4"
                                >
                                    <h3
                                        class="text-tg-text-strong text-xl font-normal uppercase sm:text-2xl"
                                        style="font-family: var(--tg-display)"
                                    >
                                        {{ venue.name }}
                                    </h3>
                                    <span
                                        class="text-tg-accent font-mono text-xs font-bold"
                                        >{{ venue.num }}</span
                                    >
                                </div>
                                <div
                                    class="text-tg-accent text-xs font-semibold"
                                >
                                    {{ venue.area }}
                                </div>
                                <div class="text-tg-text-muted text-xs">
                                    {{ venue.address }} · {{ venue.role }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Single Dominant Match Photograph -->
                    <div
                        class="lg:col-span-6"
                        data-reveal="right"
                        style="--d: 150ms"
                    >
                        <div
                            class="border-tg-border bg-tg-bg-deep group relative w-full cursor-pointer overflow-hidden rounded-xl border shadow-xl transition-all duration-300 hover:border-tg-accent/70"
                            role="button"
                            tabindex="0"
                            aria-label="View full photo of matchday fixture action"
                            @click="
                                openImagePreview({
                                    url: '/images/club/matchday-fixture-action-topgrade-london-fc.webp',
                                    alt: 'TopGrade London FC matchday action',
                                    title: 'Matchday Action · Competitive League Play',
                                    subtitle:
                                        'Competitive grassroots league fixture across London venues.',
                                })
                            "
                            @keydown.enter="
                                openImagePreview({
                                    url: '/images/club/matchday-fixture-action-topgrade-london-fc.webp',
                                    alt: 'TopGrade London FC matchday action',
                                    title: 'Matchday Action · Competitive League Play',
                                    subtitle:
                                        'Competitive grassroots league fixture across London venues.',
                                })
                            "
                        >
                            <img
                                src="/images/club/matchday-fixture-action-topgrade-london-fc.webp"
                                alt="TopGrade London FC matchday action"
                                class="block h-auto w-full select-none"
                                loading="lazy"
                            />
                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                            />
                            <span
                                class="absolute right-3 top-3 flex items-center gap-1.5 rounded-full border border-white/20 bg-black/75 px-3 py-1.5 text-xs text-white shadow-lg backdrop-blur-sm transition-all duration-200 group-hover:border-tg-accent group-hover:bg-black/90"
                            >
                                <Maximize2 class="text-tg-accent size-3.5" />
                                <span
                                    class="text-[11px] font-medium uppercase tracking-wider"
                                    >Full View</span
                                >
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 09 — FINAL CTA: Train With TopGrade -->
        <section
            id="cta"
            class="relative z-10 flex justify-center px-4 py-20 text-center sm:px-6 sm:py-28 lg:px-8"
            style="background: var(--surface-dense)"
        >
            <div
                data-reveal="scale"
                class="cta-frame bg-tg-bg-deep border-tg-border relative flex w-full max-w-5xl flex-col items-center justify-center overflow-hidden rounded-sm border p-10 text-white shadow-2xl sm:p-20"
            >
                <img
                    src="/images/club/floodlit-match-action-topgrade-london-fc.webp"
                    alt=""
                    aria-hidden="true"
                    class="absolute inset-0 h-full w-full object-cover opacity-25"
                />
                <div class="absolute inset-0 bg-black/60" />

                <div class="relative z-10 mx-auto max-w-2xl space-y-4">
                    <span
                        class="bg-tg-bg border-tg-border text-tg-accent rounded-xs inline-block border px-3 py-1 text-xs font-bold uppercase tracking-widest"
                    >
                        TopGrade London FC
                    </span>
                    <h2
                        class="font-normal uppercase tracking-tight text-white"
                        style="
                            font-family: var(--tg-display);
                            font-size: clamp(2.6rem, 7vw, 5.5rem);
                            line-height: 0.9;
                        "
                    >
                        Train With TopGrade
                    </h2>
                    <p
                        class="text-tg-paper mx-auto max-w-lg text-sm opacity-90 sm:text-base"
                    >
                        Join our weekly training sessions in Tottenham.
                        Structured coaching for youth players aged U7 to U16.
                    </p>
                    <div
                        class="flex flex-col justify-center gap-3 pt-4 sm:flex-row sm:gap-4"
                    >
                        <Link
                            href="/bookings"
                            class="tg-btn tg-focus justify-center"
                        >
                            <span>Find a Session</span>
                            <ArrowRight class="h-4 w-4" />
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
        <div
            v-if="blocks && blocks.length > 0"
            class="relative z-10 mx-auto max-w-7xl px-4 py-8"
        >
            <BlockRenderer :blocks="blocks" />
        </div>

        <ImagePreviewModal
            v-model:open="previewModalOpen"
            :image="previewImage"
        />
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

/* Subtle 3D Crest Floating Motion (calm, elegant, no continuous spinning) */
@keyframes crestFloat {
    0%,
    100% {
        transform: translate3d(0, 0, 0) rotate(0deg);
    }
    50% {
        transform: translate3d(0, -8px, 0) rotate(0.8deg);
    }
}
.animate-crest-float {
    animation: crestFloat 7s ease-in-out infinite;
}

/* Reduced Motion Override */
@media (prefers-reduced-motion: reduce) {
    .animate-hero-drop,
    .animate-hero-rise,
    .animate-hero-fade,
    .animate-marquee,
    .animate-crest-float {
        animation: none !important;
        transform: none !important;
        opacity: 1 !important;
    }
}
</style>
