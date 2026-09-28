<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, ChevronLeft, ChevronRight } from '@lucide/vue';
import MomentMedia from './MomentMedia.vue';

interface MomentImage {
    id: number;
    url: string;
    name: string;
    mime_type?: string | null;
    is_video?: boolean;
}

interface FeaturedMomentData {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    published_at: string | null;
    images_count: number;
    cover_url: string | null;
    cover_mime?: string | null;
    is_video?: boolean;
    images: MomentImage[];
}

const props = defineProps<{
    featured: FeaturedMomentData | null;
}>();

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
    <section
        v-if="featured && carouselImages.length > 0"
        aria-label="Featured Moment Gallery"
        class="relative border-b border-tg-border/80 bg-black overflow-hidden pt-4 pb-10 sm:pt-6 sm:pb-14"
        @mouseenter="onHeroMouseEnter"
        @mouseleave="onHeroMouseLeave"
        @touchstart="onTouchStart"
        @touchend="onTouchEnd"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cinematic Frame with Restrained Depth -->
            <div
                class="relative w-full aspect-4/3 sm:aspect-16/10 md:aspect-21/10 min-h-[480px] max-h-[720px] rounded-2xl overflow-hidden border border-tg-border bg-tg-bg-deep shadow-2xl"
            >
                <!-- Carousel Slide Images/Videos -->
                <div
                    v-for="(img, idx) in carouselImages"
                    :key="img.id"
                    class="absolute inset-0 transition-opacity duration-700 ease-out"
                    :class="idx === currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
                >
                    <MomentMedia
                        :src="img.url"
                        :alt="img.name || featured?.title || 'Featured moment'"
                        :is-video="Boolean(img.is_video)"
                        :poster="featured?.cover_url"
                        :loading="idx === 0 ? 'eager' : 'lazy'"
                        :active="idx === currentSlide"
                        fit-class="object-cover"
                        class="scale-[1.02] transition-transform duration-1000 ease-out"
                    />
                </div>

                <!-- Flat Translucent Layer (No Gradients) -->
                <div class="absolute inset-0 z-15 pointer-events-none bg-black/55" />

                <!-- Content Overlay: Editorial Hierarchy -->
                <div class="absolute inset-0 z-20 p-6 sm:p-10 md:p-14 flex flex-col justify-between pointer-events-none">
                    <!-- Top-Left Story Eyebrow & Monumental Heading -->
                    <div class="max-w-2xl pointer-events-auto">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 rounded-full" style="background: var(--tg-accent);" />
                            <span class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-tg-accent font-mono">
                                OUR STORY. THEIR JOURNEY.
                            </span>
                        </div>

                        <!-- Monumental Headline -->
                        <h1
                            class="font-normal uppercase tracking-tight text-white mb-3"
                            style="
                                font-family: var(--tg-display);
                                font-size: clamp(2.8rem, 8vw, 6.5rem);
                                line-height: 0.9;
                                text-shadow: 0 4px 24px rgba(0, 0, 0, 0.6);
                            "
                        >
                            Moments
                        </h1>

                        <!-- Editorial Subheadline as instructed -->
                        <p
                            class="text-sm sm:text-base md:text-lg text-gray-200 font-normal leading-relaxed max-w-xl mb-6"
                            style="text-shadow: 0 2px 12px rgba(0, 0, 0, 0.5);"
                        >
                            The story of TopGrade, one moment at a time. Matchdays, training, and player journeys.
                        </p>

                        <!-- Primary CTA Button -->
                        <Link
                            v-if="featured"
                            :href="`/moments/${featured.slug}`"
                            class="inline-flex items-center gap-2.5 rounded-full border border-tg-accent/80 bg-black/60 hover:bg-tg-accent hover:text-black px-6 py-2.5 text-xs sm:text-sm font-semibold tracking-wide text-white transition-all duration-200 shadow-md group"
                        >
                            <span>Explore Story</span>
                            <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                        </Link>
                    </div>

                    <!-- Bottom Meta Bar: Slide Counter + Navigation Controls -->
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pt-6 border-t border-white/10 pointer-events-auto">
                        <!-- Left: Slide Counter + Progress Track -->
                        <div class="flex items-center gap-3">
                            <div class="font-mono text-xs sm:text-sm font-bold tracking-widest text-white/90 select-none">
                                {{ String(currentSlide + 1).padStart(2, '0') }} / {{ String(carouselImages.length).padStart(2, '0') }}
                            </div>

                            <!-- Progress Track -->
                            <div class="w-24 sm:w-36 h-1 rounded-full bg-white/20 overflow-hidden relative">
                                <div
                                    class="h-full bg-tg-accent transition-all duration-500 ease-out"
                                    :style="{ width: carouselImages.length ? `${((currentSlide + 1) / carouselImages.length) * 100}%` : '0%' }"
                                />
                            </div>
                        </div>

                        <!-- Right: Story Context & Circular Prev/Next Controls -->
                        <div class="flex items-center gap-4">
                            <div class="hidden md:block text-right">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-tg-accent font-mono">
                                    {{ featured?.published_at || 'Matchday' }}
                                </div>
                                <div class="text-xs text-white/90 font-medium truncate max-w-xs">
                                    {{ featured?.title || '' }}
                                </div>
                            </div>

                            <!-- Controls -->
                            <div v-if="canAutoplay" class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="prevSlide"
                                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border border-white/25 hover:border-tg-accent bg-black/60 hover:bg-black text-white flex items-center justify-center transition-all active:scale-95 shadow-md focus:outline-none focus:ring-2 focus:ring-tg-accent"
                                    aria-label="Previous slide"
                                >
                                    <ChevronLeft class="w-4 h-4 sm:w-5 sm:h-5" />
                                </button>
                                <button
                                    type="button"
                                    @click="nextSlide"
                                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border border-white/25 hover:border-tg-accent bg-black/60 hover:bg-black text-white flex items-center justify-center transition-all active:scale-95 shadow-md focus:outline-none focus:ring-2 focus:ring-tg-accent"
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

    <!-- Fallback Opening when no carousel is available -->
    <section
        v-else
        class="relative border-b border-tg-border/80 bg-black overflow-hidden pt-12 pb-14 sm:pt-16 sm:pb-20"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full" style="background: var(--tg-accent);" />
                    <span class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-tg-accent font-mono">
                        OUR STORY. THEIR JOURNEY.
                    </span>
                </div>
                <h1
                    class="font-normal uppercase tracking-tight text-white mb-3"
                    style="
                        font-family: var(--tg-display);
                        font-size: clamp(2.8rem, 8vw, 6.5rem);
                        line-height: 0.9;
                    "
                >
                    Moments
                </h1>
                <p class="text-sm sm:text-base md:text-lg text-gray-200 font-normal leading-relaxed max-w-xl">
                    The story of TopGrade, one moment at a time. Players. Pathways. Progress.
                </p>
            </div>
        </div>
    </section>
</template>
