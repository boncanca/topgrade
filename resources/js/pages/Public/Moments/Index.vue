<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import FeaturedMoment from '@/components/Public/Moments/FeaturedMoment.vue';
import MomentFilters from '@/components/Public/Moments/MomentFilters.vue';
import MomentGallery from '@/components/Public/Moments/MomentGallery.vue';
import { ArrowRight } from '@lucide/vue';

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

interface MomentListItem {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    published_at: string | null;
    images_count: number;
    cover_url: string | null;
    cover_mime?: string | null;
    is_video?: boolean;
    featured: boolean;
    media?: MomentImage[];
}

const props = defineProps<{
    featured: FeaturedMomentData | null;
    moments: MomentListItem[];
}>();

defineOptions({
    layout: PublicLayout,
});

/* ─── Search Filter Logic ─── */
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
</script>

<template>
    <SeoHead
        title="Moments | TopGrade London FC"
        description="Our Story. Their Journey. Matchdays, training, teams and the visual stories that define TopGrade London FC."
        path="/moments"
        :image="featured?.cover_url || undefined"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text selection:bg-tg-accent selection:text-white">
        <!-- 01 — HERO OPENING: Strong Featured Opening Moment -->
        <FeaturedMoment :featured="featured" />

        <!-- 02 — ARCHIVE HEADER & SEARCH -->
        <MomentFilters
            v-model="searchQuery"
            :count="filteredMoments.length"
        />

        <!-- 03 — EDITORIAL VISUAL ARCHIVE -->
        <MomentGallery
            :moments="filteredMoments"
            :search-query="searchQuery"
            @clear-search="searchQuery = ''"
        />

        <!-- 04 — BOTTOM CLUB SIGN-OFF (Quiet, Non-Competing) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 sm:pb-24">
            <section class="rounded-xl overflow-hidden border border-tg-border bg-black relative">
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <img
                        src="/images/club/team-huddle.jpg"
                        alt=""
                        class="w-full h-full object-cover object-right md:object-center opacity-20 select-none scale-[1.02]"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 bg-black/90" />
                </div>

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
        </div>
    </div>
</template>
