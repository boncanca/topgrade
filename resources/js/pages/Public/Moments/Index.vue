<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import {
    ArrowRight,
    Camera,
    ChevronLeft,
    ChevronRight,
    Search,
    Sparkles,
} from '@lucide/vue';

interface MomentImage {
    id: number;
    url: string;
    name: string;
}

interface FeaturedMoment {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    published_at: string | null;
    images_count: number;
    cover_url: string | null;
    images: MomentImage[];
}

interface MomentListItem {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    published_at: string | null;
    images_count: number;
    cover_url: string | null;
    featured: boolean;
}

const props = defineProps<{
    featured: FeaturedMoment | null;
    moments: MomentListItem[];
}>();

defineOptions({
    layout: PublicLayout,
});

/* ─── Hero Carousel State & Autoplay Logic ─── */
const currentSlide = ref(0);
const isPaused = ref(false);
const reduceMotion = ref(false);
let autoplayTimer: ReturnType<typeof setInterval> | null = null;
let touchStartX = 0;
let touchEndX = 0;

const carouselImages = computed(() => {
    if (!props.featured || !props.featured.images || props.featured.images.length === 0) {
        return [];
    }
    return props.featured.images;
});

const canAutoplay = computed(() => {
    return carouselImages.value.length > 1;
});

function resetTimer(): void {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }

    if (reduceMotion.value) {
        return;
    }

    if (canAutoplay.value && !isPaused.value && document.visibilityState !== 'hidden') {
        autoplayTimer = setInterval(() => {
            nextSlide();
        }, 5000);
    }
}

function nextSlide(): void {
    if (carouselImages.value.length <= 1) return;
    currentSlide.value = (currentSlide.value + 1) % carouselImages.value.length;
    resetTimer();
}

function prevSlide(): void {
    if (carouselImages.value.length <= 1) return;
    currentSlide.value =
        (currentSlide.value - 1 + carouselImages.value.length) % carouselImages.value.length;
    resetTimer();
}

function goToSlide(idx: number): void {
    currentSlide.value = idx;
    resetTimer();
}

function onHeroMouseEnter(): void {
    isPaused.value = true;
    if (autoplayTimer) clearInterval(autoplayTimer);
}

function onHeroMouseLeave(): void {
    isPaused.value = false;
    resetTimer();
}

function onTouchStart(e: TouchEvent): void {
    touchStartX = e.changedTouches[0].screenX;
}

function onTouchEnd(e: TouchEvent): void {
    touchEndX = e.changedTouches[0].screenX;
    const diff = touchEndX - touchStartX;
    if (Math.abs(diff) > 40) {
        if (diff > 0) {
            prevSlide();
        } else {
            nextSlide();
        }
    }
}

function onVisibilityChange(): void {
    if (document.visibilityState === 'hidden') {
        if (autoplayTimer) clearInterval(autoplayTimer);
    } else {
        resetTimer();
    }
}

/* ─── Search Filter Logic (Derived from Real Data) ─── */
const searchQuery = ref('');

const filteredMoments = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.moments;
    }
    const q = searchQuery.value.toLowerCase().trim();
    return props.moments.filter((item) => {
        const titleMatch = item.title.toLowerCase().includes(q);
        const descMatch = item.description?.toLowerCase().includes(q);
        return titleMatch || descMatch;
    });
});

/* ─── TopGrade Ball Card Interaction Pointer Motif ─── */
const hoveredCardId = ref<number | null>(null);
const pointerOffsets = ref<{ x: number; y: number }>({ x: 0, y: 0 });

function onCardMouseEnter(id: number): void {
    hoveredCardId.value = id;
}

function onCardMouseLeave(): void {
    hoveredCardId.value = null;
    pointerOffsets.value = { x: 0, y: 0 };
}

function onCardMouseMove(e: MouseEvent): void {
    if (reduceMotion.value) return;
    const target = e.currentTarget as HTMLElement;
    if (!target) return;
    const rect = target.getBoundingClientRect();
    const relX = (e.clientX - rect.left - rect.width / 2) / (rect.width / 2);
    const relY = (e.clientY - rect.top - rect.height / 2) / (rect.height / 2);
    pointerOffsets.value = {
        x: Math.round(relX * 4),
        y: Math.round(relY * 4),
    };
}

onMounted(() => {
    reduceMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.addEventListener('visibilitychange', onVisibilityChange);
    resetTimer();
});

onUnmounted(() => {
    document.removeEventListener('visibilitychange', onVisibilityChange);
    if (autoplayTimer) clearInterval(autoplayTimer);
});
</script>

<template>
    <SeoHead
        title="Moments | TopGrade London FC"
        description="Our Story. Their Journey. Matchdays, training, teams and the visual stories that define TopGrade London FC."
        path="/moments"
        :image="featured?.cover_url || undefined"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text selection:bg-tg-accent selection:text-white">
        <!-- 01 — HERO SECTION: Massive Cinematic Visual Anchor (Matching Reference) -->
        <section
            v-if="featured && carouselImages.length > 0"
            aria-label="Featured Moment Gallery"
            class="relative border-b border-tg-border/80 bg-black overflow-hidden pt-4 pb-12 sm:pt-6 sm:pb-16"
            @mouseenter="onHeroMouseEnter"
            @mouseleave="onHeroMouseLeave"
            @touchstart="onTouchStart"
            @touchend="onTouchEnd"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Massive Cinematic Carousel Frame -->
                <div class="relative w-full aspect-4/3 sm:aspect-16/10 md:aspect-21/10 min-h-[500px] max-h-[740px] rounded-2xl overflow-hidden border border-tg-border bg-tg-bg-deep shadow-2xl">
                    <!-- Carousel Slide Images -->
                    <div
                        v-for="(img, idx) in carouselImages"
                        :key="img.id"
                        class="absolute inset-0 transition-opacity duration-700 ease-out"
                        :class="idx === currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
                    >
                        <img
                            :src="img.url"
                            :alt="img.name || featured.title"
                            class="w-full h-full object-cover object-center scale-[1.02] transition-transform duration-1000 ease-out"
                            :loading="idx === 0 ? 'eager' : 'lazy'"
                        />
                    </div>

                    <!-- Controlled Dark Surface Overlay for Contrast (No Gradients) -->
                    <div class="absolute inset-0 z-15 pointer-events-none bg-black/60" />

                    <!-- Hero Content: Monumental Editorial Typography (Left/Top Alignment) -->
                    <div class="absolute inset-0 z-20 p-6 sm:p-10 md:p-14 flex flex-col justify-between pointer-events-none">
                        <!-- Top-Left Story Badge & Monumental Heading -->
                        <div class="max-w-2xl pointer-events-auto">
                            <!-- Eyebrow Tag -->
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-tg-accent font-mono">
                                    OUR STORY. THEIR JOURNEY.
                                </span>
                            </div>

                            <!-- Monumental Headline -->
                            <h1
                                class="font-normal uppercase tracking-tight text-white mb-4"
                                style="
                                    font-family: var(--tg-display);
                                    font-size: clamp(2.8rem, 8vw, 6.5rem);
                                    line-height: 0.9;
                                    text-shadow: 0 4px 24px rgba(0, 0, 0, 0.6);
                                "
                            >
                                Moments
                            </h1>

                            <!-- Editorial Subheadline -->
                            <p class="text-sm sm:text-base md:text-lg text-gray-200 font-normal leading-relaxed max-w-xl mb-6">
                                Players. Pathways. Progress. A look into the people, places and moments that make TopGrade London FC.
                            </p>

                            <!-- Primary CTA Button -->
                            <Link
                                :href="`/moments/${featured.slug}`"
                                class="inline-flex items-center gap-2.5 rounded-full border border-tg-accent/80 bg-black/60 hover:bg-tg-accent hover:text-black px-6 py-2.5 text-xs sm:text-sm font-semibold tracking-wide text-white transition-all duration-200 shadow-md group"
                            >
                                <span>Explore the Gallery</span>
                                <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                            </Link>
                        </div>

                        <!-- Bottom Meta Bar: Slide Counter with TopGrade Ball Motif + Navigation Controls -->
                        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pt-6 border-t border-white/10 pointer-events-auto">
                            <!-- Left: Slide Counter + Progress Bar + TopGrade Ball Anchor Motif -->
                            <div class="flex items-center gap-3">
                                <div class="font-mono text-xs sm:text-sm font-bold tracking-widest text-white/90 select-none">
                                    {{ String(currentSlide + 1).padStart(2, '0') }} / {{ String(carouselImages.length).padStart(2, '0') }}
                                </div>

                                <!-- Progress Line Track -->
                                <div class="w-24 sm:w-36 h-1 rounded-full bg-white/20 overflow-hidden relative">
                                    <div
                                        class="h-full bg-tg-accent transition-all duration-500 ease-out"
                                        :style="{ width: `${((currentSlide + 1) / carouselImages.length) * 100}%` }"
                                    />
                                </div>

                                <!-- Subtle TopGrade Ball Motif as Slide Anchor -->
                                <img
                                    src="/ball-optimized.webp"
                                    alt=""
                                    class="w-4 h-4 sm:w-5 sm:h-5 object-contain select-none opacity-80"
                                    aria-hidden="true"
                                />
                            </div>

                            <!-- Right: Current Slide Context & Minimal Circular Controls -->
                            <div class="flex items-center gap-4">
                                <div class="hidden md:block text-right">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-tg-accent">
                                        {{ featured.published_at || 'Match Day' }}
                                    </div>
                                    <div class="text-xs text-white/90 font-medium truncate max-w-xs">
                                        {{ featured.title }}
                                    </div>
                                </div>

                                <!-- Circular Prev / Next Navigation Arrows -->
                                <div v-if="canAutoplay" class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="prevSlide"
                                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border border-white/25 hover:border-tg-accent bg-black/60 hover:bg-black text-white flex items-center justify-center transition-all active:scale-95 shadow-md focus:outline-hidden focus:ring-2 focus:ring-tg-accent"
                                        aria-label="Previous slide"
                                    >
                                        <ChevronLeft class="w-4 h-4 sm:w-5 sm:h-5" />
                                    </button>
                                    <button
                                        type="button"
                                        @click="nextSlide"
                                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border border-white/25 hover:border-tg-accent bg-black/60 hover:bg-black text-white flex items-center justify-center transition-all active:scale-95 shadow-md focus:outline-hidden focus:ring-2 focus:ring-tg-accent"
                                        aria-label="Next slide"
                                    >
                                        <ChevronRight class="w-4 h-4 sm:w-5 sm:h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 02 — ARCHIVE HEADER & COMPACT SEARCH: Clean, Photography-First -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-2">
            <div class="flex items-center justify-between gap-4 border-b border-tg-border/70 pb-4">
                <div class="flex items-baseline gap-3">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-white font-mono">
                        Archive
                    </h2>
                    <span class="text-xs text-tg-text-muted font-mono">
                        {{ filteredMoments.length }} {{ filteredMoments.length === 1 ? 'story' : 'stories' }}
                    </span>
                </div>

                <!-- Compact Sleek Search -->
                <div class="relative w-48 sm:w-64">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-tg-text-muted" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search stories..."
                        class="w-full rounded-full border border-tg-border bg-tg-bg-deep pl-8 pr-3 py-1.5 text-xs text-white placeholder:text-tg-text-muted outline-none focus:border-tg-accent focus:ring-1 focus:ring-tg-accent transition-all"
                    />
                </div>
            </div>
        </section>

        <!-- 03 — EDITORIAL GALLERY GRID (Asymmetrical Rhythm Matching Reference) -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-14">
            <!-- Empty State -->
            <div
                v-if="filteredMoments.length === 0"
                class="rounded-xl border border-dashed border-tg-border p-12 text-center bg-tg-bg-deep/40 my-8"
            >
                <Camera class="w-10 h-10 mx-auto text-tg-text-muted/60 mb-3" />
                <h3
                    class="text-lg uppercase text-tg-text-strong font-normal mb-2"
                    style="font-family: var(--tg-display);"
                >
                    No Moments Found
                </h3>
                <p class="text-sm text-tg-text-muted max-w-sm mx-auto">
                    {{ searchQuery ? `No stories matching "${searchQuery}".` : 'Club life, matchdays and training — coming soon.' }}
                </p>
                <button
                    v-if="searchQuery"
                    type="button"
                    @click="searchQuery = ''"
                    class="mt-4 text-xs font-semibold uppercase text-tg-accent hover:underline"
                >
                    Clear search
                </button>
            </div>

            <!-- Asymmetrical Editorial Grid Layout -->
            <div
                v-else
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 items-stretch"
            >
                <div
                    v-for="(item, idx) in filteredMoments"
                    :key="item.id"
                    class="relative group"
                    :class="[
                        // First item gets tall prominent presentation if grid has multiple items
                        idx === 0 && filteredMoments.length >= 3 ? 'md:row-span-2' : ''
                    ]"
                    @mouseenter="onCardMouseEnter(item.id)"
                    @mouseleave="onCardMouseLeave"
                    @mousemove="onCardMouseMove"
                >
                    <!-- TopGrade Ball Interaction Pointer Motif (Sliding out on hover) -->
                    <div
                        class="absolute -top-3 -right-3 z-30 pointer-events-none transition-all duration-300 ease-out select-none"
                        :class="
                            hoveredCardId === item.id
                                ? 'opacity-100 scale-100 translate-y-0 rotate-12'
                                : 'opacity-0 scale-75 translate-y-2 rotate-0'
                        "
                        :style="
                            hoveredCardId === item.id && !reduceMotion
                                ? {
                                      transform: `translate3d(${pointerOffsets.x}px, ${pointerOffsets.y}px, 0) rotate(${12 + pointerOffsets.x * 2}deg)`,
                                  }
                                : undefined
                        "
                        aria-hidden="true"
                    >
                        <img
                            src="/ball-optimized.webp"
                            alt=""
                            class="w-7 h-7 sm:w-8 sm:h-8 object-contain drop-shadow-md"
                        />
                    </div>

                    <!-- The Moment Card -->
                    <Link
                        :href="`/moments/${item.slug}`"
                        class="block w-full h-full rounded-xl overflow-hidden border border-tg-border bg-tg-bg-deep relative transition-all duration-300 group-hover:border-tg-accent group-hover:shadow-xl"
                    >
                        <!-- Card Media Container -->
                        <div
                            class="relative w-full overflow-hidden bg-black flex items-center justify-center"
                            :class="[
                                idx === 0 && filteredMoments.length >= 3
                                    ? 'h-full min-h-[460px] md:min-h-[580px]'
                                    : 'aspect-16/10 sm:aspect-4/3 min-h-[260px]'
                            ]"
                        >
                            <!-- Cover Photo with Subtle Scale on Hover -->
                            <img
                                v-if="item.cover_url"
                                :src="item.cover_url"
                                :alt="item.title"
                                class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                                loading="lazy"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center text-tg-text-muted/40 bg-tg-bg-deep">
                                <Camera class="w-10 h-10" />
                            </div>

                            <!-- Card Content (Bottom Solid Surface Overlay - No Gradients) -->
                            <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6 z-10 flex flex-col justify-end bg-black/85 border-t border-white/5">
                                <!-- Card Title -->
                                <h3
                                    class="text-lg sm:text-xl font-normal uppercase text-white tracking-tight leading-tight group-hover:text-tg-accent transition-colors"
                                    style="font-family: var(--tg-display);"
                                >
                                    {{ item.title }}
                                </h3>

                                <!-- Short Description -->
                                <p
                                    v-if="item.description"
                                    class="text-xs text-gray-300 mt-1.5 line-clamp-2 leading-relaxed font-normal"
                                >
                                    {{ item.description }}
                                </p>

                                <!-- Footer Bar: Photos Count + Arrow -->
                                <div class="pt-3 mt-3 border-t border-white/10 flex items-center justify-between text-xs text-white/80">
                                    <span class="font-medium text-[11px] text-gray-400 font-mono">
                                        {{ item.published_at ? `${item.published_at} · ` : '' }}{{ item.images_count }} {{ item.images_count === 1 ? 'photo' : 'photos' }}
                                    </span>
                                    <div class="flex items-center gap-1 font-semibold text-white group-hover:text-tg-accent">
                                        <ArrowRight class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- 04 — BOTTOM CLUB SIGN-OFF (Quiet, Non-Competing) -->
            <section class="mt-14 sm:mt-20 rounded-xl overflow-hidden border border-tg-border bg-black relative">
                <!-- Background Imagery: Subtle Silhouette Squad Huddle -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <img
                        src="/images/club/team-huddle.jpg"
                        alt=""
                        class="w-full h-full object-cover object-right md:object-center opacity-20 select-none scale-[1.02]"
                    />
                    <!-- Controlled Solid Dark Surface (No Gradients) -->
                    <div class="absolute inset-0 bg-black/90" />
                </div>

                <!-- Banner Content: Restrained & Editorial -->
                <div class="relative z-10 p-6 sm:p-8 md:p-10 max-w-xl">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-tg-accent font-mono mb-1.5">
                        TOPGRADE LONDON FC
                    </div>
                    <h2
                        class="text-xl sm:text-2xl md:text-3xl font-normal uppercase text-white tracking-tight mb-2"
                        style="font-family: var(--tg-display); line-height: 1;"
                    >
                        Join TopGrade London FC.
                    </h2>
                    <p class="text-xs text-gray-300 leading-relaxed mb-5 max-w-md">
                        North and East London youth football built through the game. Experience our coaching, culture, and competitive matchdays.
                    </p>
                    <Link
                        href="/bookings"
                        class="tg-btn text-xs py-2 px-5 shadow-sm inline-flex items-center gap-2"
                    >
                        <span>Book a Session</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>
            </section>
        </main>
    </div>
</template>
