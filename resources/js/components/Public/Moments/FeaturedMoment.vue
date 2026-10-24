<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, ChevronLeft, ChevronRight } from '@lucide/vue';
import { ref, computed, onMounted, onUnmounted } from 'vue';
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
    if (
        !props.featured ||
        !props.featured.images ||
        props.featured.images.length === 0
    ) {
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

    if (
        canAutoplay.value &&
        !isPaused.value &&
        document.visibilityState !== 'hidden'
    ) {
        autoplayTimer = setInterval(() => {
            nextSlide();
        }, 5000);
    }
}

function nextSlide(): void {
    if (carouselImages.value.length <= 1) {
        return;
    }

    currentSlide.value = (currentSlide.value + 1) % carouselImages.value.length;
    resetTimer();
}

function prevSlide(): void {
    if (carouselImages.value.length <= 1) {
        return;
    }

    currentSlide.value =
        (currentSlide.value - 1 + carouselImages.value.length) %
        carouselImages.value.length;
    resetTimer();
}

function onHeroMouseEnter(): void {
    isPaused.value = true;

    if (autoplayTimer) {
        clearInterval(autoplayTimer);
    }
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
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
        }
    } else {
        resetTimer();
    }
}

onMounted(() => {
    reduceMotion.value = window.matchMedia(
        '(prefers-reduced-motion: reduce)',
    ).matches;
    document.addEventListener('visibilitychange', onVisibilityChange);
    resetTimer();
});

onUnmounted(() => {
    document.removeEventListener('visibilitychange', onVisibilityChange);

    if (autoplayTimer) {
        clearInterval(autoplayTimer);
    }
});
</script>

<template>
    <section
        v-if="featured && carouselImages.length > 0"
        aria-label="Featured Moment Gallery"
        class="border-tg-border/80 relative overflow-hidden border-b bg-black pb-10 pt-4 sm:pb-14 sm:pt-6"
        @mouseenter="onHeroMouseEnter"
        @mouseleave="onHeroMouseLeave"
        @touchstart="onTouchStart"
        @touchend="onTouchEnd"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Cinematic Frame with Restrained Depth -->
            <div
                class="aspect-4/3 sm:aspect-16/10 md:aspect-21/10 border-tg-border bg-tg-bg-deep relative max-h-[720px] min-h-[480px] w-full overflow-hidden rounded-2xl border shadow-2xl"
            >
                <!-- Carousel Slide Images/Videos -->
                <div
                    v-for="(img, idx) in carouselImages"
                    :key="img.id"
                    class="absolute inset-0 transition-opacity duration-700 ease-out"
                    :class="
                        idx === currentSlide
                            ? 'z-10 opacity-100'
                            : 'pointer-events-none z-0 opacity-0'
                    "
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
                <div
                    class="z-15 pointer-events-none absolute inset-0 bg-black/55"
                />

                <!-- Content Overlay: Editorial Hierarchy -->
                <div
                    class="pointer-events-none absolute inset-0 z-20 flex flex-col justify-between p-6 sm:p-10 md:p-14"
                >
                    <!-- Top-Left Story Eyebrow & Monumental Heading -->
                    <div class="pointer-events-auto max-w-2xl">
                        <div class="mb-3 flex items-center gap-2">
                            <span
                                class="h-2 w-2 rounded-full"
                                style="background: var(--tg-accent)"
                            />
                            <span
                                class="text-tg-accent font-mono text-[11px] font-bold uppercase tracking-widest sm:text-xs"
                            >
                                OUR STORY. THEIR JOURNEY.
                            </span>
                        </div>

                        <!-- Monumental Headline -->
                        <h1
                            class="mb-3 font-normal uppercase tracking-tight text-white"
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
                            class="mb-6 max-w-xl text-sm font-normal leading-relaxed text-gray-200 sm:text-base md:text-lg"
                            style="text-shadow: 0 2px 12px rgba(0, 0, 0, 0.5)"
                        >
                            The story of TopGrade, one moment at a time.
                            Matchdays, training, and player journeys.
                        </p>

                        <!-- Primary CTA Button -->
                        <Link
                            v-if="featured"
                            :href="`/moments/${featured.slug}`"
                            class="border-tg-accent/80 hover:bg-tg-accent group inline-flex items-center gap-2.5 rounded-full border bg-black/60 px-6 py-2.5 text-xs font-semibold tracking-wide text-white shadow-md transition-all duration-200 hover:text-black sm:text-sm"
                        >
                            <span>Explore Story</span>
                            <ArrowRight
                                class="h-4 w-4 transition-transform group-hover:translate-x-1"
                            />
                        </Link>
                    </div>

                    <!-- Bottom Meta Bar: Slide Counter + Navigation Controls -->
                    <div
                        class="pointer-events-auto flex flex-col justify-between gap-4 border-t border-white/10 pt-6 sm:flex-row sm:items-end"
                    >
                        <!-- Left: Slide Counter + Progress Track -->
                        <div class="flex items-center gap-3">
                            <div
                                class="select-none font-mono text-xs font-bold tracking-widest text-white/90 sm:text-sm"
                            >
                                {{ String(currentSlide + 1).padStart(2, '0') }}
                                /
                                {{
                                    String(carouselImages.length).padStart(
                                        2,
                                        '0',
                                    )
                                }}
                            </div>

                            <!-- Progress Track -->
                            <div
                                class="relative h-1 w-24 overflow-hidden rounded-full bg-white/20 sm:w-36"
                            >
                                <div
                                    class="bg-tg-accent h-full transition-all duration-500 ease-out"
                                    :style="{
                                        width: carouselImages.length
                                            ? `${((currentSlide + 1) / carouselImages.length) * 100}%`
                                            : '0%',
                                    }"
                                />
                            </div>
                        </div>

                        <!-- Right: Story Context & Circular Prev/Next Controls -->
                        <div class="flex items-center gap-4">
                            <div class="hidden text-right md:block">
                                <div
                                    class="text-tg-accent font-mono text-[11px] font-bold uppercase tracking-wider"
                                >
                                    {{ featured?.published_at || 'Matchday' }}
                                </div>
                                <div
                                    class="max-w-xs truncate text-xs font-medium text-white/90"
                                >
                                    {{ featured?.title || '' }}
                                </div>
                            </div>

                            <!-- Controls -->
                            <div
                                v-if="canAutoplay"
                                class="flex items-center gap-2"
                            >
                                <button
                                    type="button"
                                    @click="prevSlide"
                                    class="hover:border-tg-accent focus:ring-tg-accent flex h-9 w-9 items-center justify-center rounded-full border border-white/25 bg-black/60 text-white shadow-md transition-all hover:bg-black focus:outline-none focus:ring-2 active:scale-95 sm:h-10 sm:w-10"
                                    aria-label="Previous slide"
                                >
                                    <ChevronLeft
                                        class="h-4 w-4 sm:h-5 sm:w-5"
                                    />
                                </button>
                                <button
                                    type="button"
                                    @click="nextSlide"
                                    class="hover:border-tg-accent focus:ring-tg-accent flex h-9 w-9 items-center justify-center rounded-full border border-white/25 bg-black/60 text-white shadow-md transition-all hover:bg-black focus:outline-none focus:ring-2 active:scale-95 sm:h-10 sm:w-10"
                                    aria-label="Next slide"
                                >
                                    <ChevronRight
                                        class="h-4 w-4 sm:h-5 sm:w-5"
                                    />
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
        class="border-tg-border/80 relative overflow-hidden border-b bg-black pb-14 pt-12 sm:pb-20 sm:pt-16"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <div class="mb-3 flex items-center gap-2">
                    <span
                        class="h-2 w-2 rounded-full"
                        style="background: var(--tg-accent)"
                    />
                    <span
                        class="text-tg-accent font-mono text-[11px] font-bold uppercase tracking-widest sm:text-xs"
                    >
                        OUR STORY. THEIR JOURNEY.
                    </span>
                </div>
                <h1
                    class="mb-3 font-normal uppercase tracking-tight text-white"
                    style="
                        font-family: var(--tg-display);
                        font-size: clamp(2.8rem, 8vw, 6.5rem);
                        line-height: 0.9;
                    "
                >
                    Moments
                </h1>
                <p
                    class="max-w-xl text-sm font-normal leading-relaxed text-gray-200 sm:text-base md:text-lg"
                >
                    The story of TopGrade, one moment at a time. Players.
                    Pathways. Progress.
                </p>
            </div>
        </div>
    </section>
</template>
