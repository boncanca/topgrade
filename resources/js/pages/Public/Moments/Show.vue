<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import {
    ArrowLeft,
    ArrowRight,
    Camera,
    ChevronLeft,
    ChevronRight,
    ExternalLink,
    Maximize2,
    X,
} from '@lucide/vue';

interface GalleryImage {
    id: number;
    url: string;
    name: string;
    is_cover: boolean;
}

interface PersonBadge {
    name: string;
    role: string | null;
    url: string | null;
}

interface MomentDetail {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    published_at: string | null;
    images_count: number;
    cover_url: string | null;
    external_link: string | null;
    external_link_label: string | null;
    people: PersonBadge[];
    seo: {
        title: string;
        description: string;
        canonical_url: string | null;
    };
}

interface RelatedMoment {
    id: number;
    title: string;
    slug: string;
    description?: string | null;
    published_at?: string | null;
    cover_url: string | null;
    images_count: number;
}

const props = defineProps<{
    moment: MomentDetail;
    gallery: GalleryImage[];
    related: RelatedMoment[];
}>();

defineOptions({
    layout: PublicLayout,
});

const activeIndex = ref(0);
const isLightbox = ref(false);
const reduceMotion = ref(false);
let touchStartX = 0;
let touchEndX = 0;

const currentImage = computed(() => {
    return props.gallery[activeIndex.value] ?? null;
});

function nextImage(): void {
    if (props.gallery.length <= 1) return;
    activeIndex.value = (activeIndex.value + 1) % props.gallery.length;
}

function prevImage(): void {
    if (props.gallery.length <= 1) return;
    activeIndex.value = (activeIndex.value - 1 + props.gallery.length) % props.gallery.length;
}

function selectImage(idx: number): void {
    activeIndex.value = idx;
}

function toggleLightbox(): void {
    isLightbox.value = !isLightbox.value;
}

function closeLightbox(): void {
    isLightbox.value = false;
}

function handleKeydown(e: KeyboardEvent): void {
    if (e.key === 'ArrowRight') {
        nextImage();
    } else if (e.key === 'ArrowLeft') {
        prevImage();
    } else if (e.key === 'Escape' && isLightbox.value) {
        closeLightbox();
    }
}

function onTouchStart(e: TouchEvent): void {
    touchStartX = e.changedTouches[0].screenX;
}

function onTouchEnd(e: TouchEvent): void {
    touchEndX = e.changedTouches[0].screenX;
    const diff = touchEndX - touchStartX;
    if (Math.abs(diff) > 40) {
        if (diff > 0) {
            prevImage();
        } else {
            nextImage();
        }
    }
}

/* ─── Card Hover Ball Pointer Motif for Related Stories ─── */
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
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <SeoHead
        :title="moment.seo.title"
        :description="moment.seo.description"
        :path="`/moments/${moment.slug}`"
        :image="moment.cover_url || undefined"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text selection:bg-tg-accent selection:text-white">
        <!-- 00 — BREADCRUMB & BACK NAV -->
        <div class="border-b border-tg-border bg-tg-bg-deep/50 px-4 sm:px-6 lg:px-8 py-3.5">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <Link
                    href="/moments"
                    class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-tg-text-muted hover:text-tg-accent transition-colors"
                >
                    <ArrowLeft class="w-3.5 h-3.5" />
                    <span>Back to all Moments</span>
                </Link>

                <div v-if="moment.published_at" class="text-xs text-tg-text-muted font-mono">
                    {{ moment.published_at }}
                </div>
            </div>
        </div>

        <!-- 01 — EDITORIAL STORY HEADER: Photography-First, Pure Content -->
        <header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-4 sm:pt-12 sm:pb-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="max-w-3xl">
                    <!-- Monumental Story Title -->
                    <h1
                        class="text-3xl sm:text-5xl md:text-6xl font-normal uppercase tracking-tight text-white"
                        style="font-family: var(--tg-display); line-height: 0.95;"
                    >
                        {{ moment.title }}
                    </h1>

                    <!-- Editorial Description -->
                    <p v-if="moment.description" class="text-sm sm:text-base text-gray-300 mt-4 leading-relaxed max-w-2xl font-normal">
                        {{ moment.description }}
                    </p>

                    <!-- Meta Line: Date & Photo Count -->
                    <div class="flex items-center gap-3 mt-3.5 text-xs text-tg-text-muted font-mono">
                        <span v-if="moment.published_at">{{ moment.published_at }}</span>
                        <span v-if="moment.published_at" class="text-tg-border">•</span>
                        <span>{{ gallery.length }} {{ gallery.length === 1 ? 'photo' : 'photos' }}</span>
                    </div>
                </div>

                <!-- External Link Action -->
                <div v-if="moment.external_link" class="shrink-0">
                    <a
                        :href="moment.external_link"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="tg-btn text-xs py-2 px-4 shadow-sm inline-flex items-center gap-2"
                    >
                        <span>{{ moment.external_link_label || 'View Details' }}</span>
                        <ExternalLink class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
        </header>

        <!-- 02 — PRIMARY IMMERSIVE GALLERY VIEWER -->
        <section
            v-if="gallery.length > 0"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-6"
            aria-label="Photo Gallery Viewer"
        >
            <div
                class="relative rounded-2xl overflow-hidden border border-tg-border bg-black shadow-2xl"
                @touchstart="onTouchStart"
                @touchend="onTouchEnd"
            >
                <!-- Main Image Display Container -->
                <div class="relative w-full aspect-4/3 sm:aspect-16/10 md:aspect-21/10 max-h-[740px] flex items-center justify-center bg-black select-none">
                    <img
                        v-if="currentImage"
                        :src="currentImage.url"
                        :alt="currentImage.name || moment.title"
                        class="w-full h-full object-contain"
                    />

                    <!-- Prev Button (Minimal Circular) -->
                    <button
                        v-if="gallery.length > 1"
                        type="button"
                        @click="prevImage"
                        class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-black/75 hover:bg-black text-white border border-white/20 hover:border-tg-accent flex items-center justify-center transition-all active:scale-95 shadow-md focus:outline-hidden focus:ring-2 focus:ring-tg-accent"
                        aria-label="Previous photo (Left Arrow)"
                    >
                        <ChevronLeft class="w-5 h-5 sm:w-6 sm:h-6" />
                    </button>

                    <!-- Next Button (Minimal Circular) -->
                    <button
                        v-if="gallery.length > 1"
                        type="button"
                        @click="nextImage"
                        class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-black/75 hover:bg-black text-white border border-white/20 hover:border-tg-accent flex items-center justify-center transition-all active:scale-95 shadow-md focus:outline-hidden focus:ring-2 focus:ring-tg-accent"
                        aria-label="Next photo (Right Arrow)"
                    >
                        <ChevronRight class="w-5 h-5 sm:w-6 sm:h-6" />
                    </button>

                    <!-- Top Bar: Counter with Ball Motif + Fullscreen Lightbox Trigger -->
                    <div class="absolute top-4 inset-x-4 sm:inset-x-6 flex justify-between items-center pointer-events-none">
                        <!-- Counter + Ball Motif (Rarest TopGrade Touch) -->
                        <div class="font-mono text-xs sm:text-sm font-bold tracking-widest text-white/90 bg-black/80 px-3.5 py-1.5 rounded-full border border-white/10 select-none shadow-md pointer-events-auto flex items-center gap-2.5">
                            <span>{{ String(activeIndex + 1).padStart(2, '0') }} / {{ String(gallery.length).padStart(2, '0') }}</span>
                            <img
                                src="/ball-optimized.webp"
                                alt=""
                                class="w-3.5 h-3.5 sm:w-4 sm:h-4 object-contain opacity-80"
                                aria-hidden="true"
                            />
                        </div>

                        <!-- Fullscreen Toggle -->
                        <button
                            type="button"
                            @click="toggleLightbox"
                            class="pointer-events-auto w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-black/80 hover:bg-black text-white border border-white/10 hover:border-tg-accent flex items-center justify-center transition-colors shadow-md focus:outline-hidden focus:ring-2 focus:ring-tg-accent"
                            title="Fullscreen Lightbox"
                        >
                            <Maximize2 class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- Thumbnail Navigation Strip: Clean Pure Photography (No Ball Clutter) -->
                <div class="border-t border-tg-border bg-tg-bg-deep p-3 sm:p-4 overflow-x-auto flex gap-3 scrollbar-thin">
                    <button
                        v-for="(img, idx) in gallery"
                        :key="img.id"
                        type="button"
                        @click="selectImage(idx)"
                        class="relative shrink-0 w-20 sm:w-24 aspect-4/3 rounded-lg overflow-hidden border transition-all duration-200"
                        :class="
                            idx === activeIndex
                                ? 'border-tg-accent ring-1 ring-tg-accent scale-105'
                                : 'border-tg-border opacity-60 hover:opacity-100 hover:border-tg-accent/50'
                        "
                        :aria-label="`View photo ${idx + 1}`"
                    >
                        <img
                            :src="img.url"
                            :alt="img.name"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        />
                    </button>
                </div>
            </div>

            <!-- Contextual People Credits: Quiet Footnote Below Viewer -->
            <div
                v-if="moment.people && moment.people.length > 0"
                class="mt-4 flex flex-wrap items-baseline gap-x-2 gap-y-1 text-xs text-tg-text-muted"
            >
                <span class="font-mono text-[10px] uppercase tracking-wider text-tg-accent">Featuring</span>
                <span class="text-white/80">
                    {{ moment.people.map(p => p.role ? `${p.name} (${p.role})` : p.name).join(' · ') }}
                </span>
            </div>
        </section>

        <!-- Empty State if no photos yet -->
        <section
            v-else
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16"
        >
            <div class="rounded-xl border border-dashed border-tg-border p-10 sm:p-14 text-center bg-tg-bg-deep/40">
                <Camera class="w-10 h-10 mx-auto text-tg-text-muted/60 mb-3" />
                <h3
                    class="text-lg uppercase text-tg-text-strong font-normal mb-2"
                    style="font-family: var(--tg-display);"
                >
                    No Photos in this Gallery Yet
                </h3>
                <p class="text-sm text-tg-text-muted max-w-sm mx-auto mb-6">
                    Visual matchday and training photos are being curated for this story. Check back soon.
                </p>
                <Link href="/moments" class="tg-btn ghost text-xs py-2 px-4 shadow-sm inline-flex items-center gap-1.5">
                    <ArrowLeft class="w-3.5 h-3.5" />
                    <span>Back to all Stories</span>
                </Link>
            </div>
        </section>

        <!-- 03 — LIGHTBOX MODAL (When Expanded - Zero Gradients, Zero Glow) -->
        <Teleport to="body">
            <div
                v-if="isLightbox && currentImage"
                class="fixed inset-0 z-50 bg-black/95 flex flex-col justify-between p-4 sm:p-6"
                role="dialog"
                aria-modal="true"
                @touchstart="onTouchStart"
                @touchend="onTouchEnd"
            >
                <!-- Top Lightbox Header -->
                <div class="flex items-center justify-between text-white pb-4 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-sm tracking-wider font-bold">
                            {{ String(activeIndex + 1).padStart(2, '0') }} / {{ String(gallery.length).padStart(2, '0') }}
                        </span>
                        <img
                            src="/ball-optimized.webp"
                            alt=""
                            class="w-4 h-4 object-contain opacity-80"
                            aria-hidden="true"
                        />
                        <span class="text-white/40">•</span>
                        <span class="text-sm uppercase font-normal tracking-wide text-white/90" style="font-family: var(--tg-display);">
                            {{ moment.title }}
                        </span>
                    </div>

                    <button
                        type="button"
                        @click="closeLightbox"
                        class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors"
                        title="Close (Escape)"
                    >
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Main Lightbox Image View -->
                <div class="relative grow flex items-center justify-center p-2 my-auto select-none">
                    <img
                        :src="currentImage.url"
                        :alt="currentImage.name || moment.title"
                        class="max-w-full max-h-[82vh] object-contain shadow-2xl"
                    />

                    <!-- Lightbox Prev / Next -->
                    <button
                        v-if="gallery.length > 1"
                        type="button"
                        @click="prevImage"
                        class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-black/75 hover:bg-black text-white border border-white/20 hover:border-tg-accent flex items-center justify-center transition-all"
                        aria-label="Previous photo"
                    >
                        <ChevronLeft class="w-6 h-6" />
                    </button>
                    <button
                        v-if="gallery.length > 1"
                        type="button"
                        @click="nextImage"
                        class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-black/75 hover:bg-black text-white border border-white/20 hover:border-tg-accent flex items-center justify-center transition-all"
                        aria-label="Next photo"
                    >
                        <ChevronRight class="w-6 h-6" />
                    </button>
                </div>

                <!-- Bottom Lightbox Caption -->
                <div class="text-center text-xs text-white/60 py-2 border-t border-white/10">
                    Use <kbd class="px-1.5 py-0.5 rounded bg-white/10 text-white font-mono">←</kbd> and <kbd class="px-1.5 py-0.5 rounded bg-white/10 text-white font-mono">→</kbd> keys to navigate · <kbd class="px-1.5 py-0.5 rounded bg-white/10 text-white font-mono">Esc</kbd> to exit
                </div>
            </div>
        </Teleport>

        <!-- 04 — RELATED / EXPLORE MORE MOMENTS -->
        <section v-if="related.length > 0" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 border-t border-tg-border mt-12">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" style="background: var(--tg-accent);" />
                    <h2
                        class="text-xl sm:text-2xl uppercase tracking-tight text-tg-text-strong font-normal"
                        style="font-family: var(--tg-display);"
                    >
                        More Stories from TopGrade
                    </h2>
                </div>

                <Link
                    href="/moments"
                    class="text-xs font-semibold uppercase tracking-wider text-tg-accent hover:underline flex items-center gap-1"
                >
                    <span>View all</span>
                    <ArrowRight class="w-3.5 h-3.5" />
                </Link>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <div
                    v-for="rel in related"
                    :key="rel.id"
                    class="relative group"
                    @mouseenter="onCardMouseEnter(rel.id)"
                    @mouseleave="onCardMouseLeave"
                    @mousemove="onCardMouseMove"
                >
                    <!-- Ball Pointer Motif on Card Hover: Small & Subtle -->
                    <div
                        class="absolute -top-3 -right-3 z-30 pointer-events-none transition-all duration-300 ease-out select-none"
                        :class="
                            hoveredCardId === rel.id
                                ? 'opacity-100 scale-100 translate-y-0 rotate-12'
                                : 'opacity-0 scale-75 translate-y-2 rotate-0'
                        "
                        :style="
                            hoveredCardId === rel.id && !reduceMotion
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

                    <Link
                        :href="`/moments/${rel.slug}`"
                        class="block w-full h-full rounded-xl overflow-hidden border border-tg-border bg-tg-bg-deep relative transition-all duration-300 group-hover:border-tg-accent group-hover:shadow-xl"
                    >
                        <div class="relative w-full aspect-16/10 overflow-hidden bg-black flex items-center justify-center">
                            <img
                                v-if="rel.cover_url"
                                :src="rel.cover_url"
                                :alt="rel.title"
                                class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                                loading="lazy"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center text-tg-text-muted/40 bg-tg-bg-deep">
                                <Camera class="w-8 h-8" />
                            </div>

                            <!-- Solid Bottom Scrim for Text Legibility (No Gradients) -->
                            <div class="absolute inset-x-0 bottom-0 p-4 sm:p-5 z-10 flex flex-col justify-end bg-black/85 border-t border-white/5">
                                <h3
                                    class="text-base font-normal uppercase text-white tracking-tight leading-tight group-hover:text-tg-accent transition-colors"
                                    style="font-family: var(--tg-display);"
                                >
                                    {{ rel.title }}
                                </h3>
                                <div class="pt-2 mt-2 border-t border-white/10 flex items-center justify-between text-xs text-white/80">
                                    <span class="text-[11px] text-gray-400 font-mono">
                                        {{ rel.images_count }} {{ rel.images_count === 1 ? 'photo' : 'photos' }}
                                    </span>
                                    <ArrowRight class="w-3.5 h-3.5 text-white group-hover:text-tg-accent transition-transform group-hover:translate-x-1" />
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
