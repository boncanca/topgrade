<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import { ref, computed } from 'vue';
import FeaturedMoment from '@/components/Public/Moments/FeaturedMoment.vue';
import MomentFilters from '@/components/Public/Moments/MomentFilters.vue';
import MomentGallery from '@/components/Public/Moments/MomentGallery.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

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
const mediaFilter = ref<'all' | 'photos' | 'videos'>('all');

const filteredMoments = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();

    return props.moments.filter((item) => {
        const matchesSearch =
            !query ||
            item.title.toLowerCase().includes(query) ||
            item.description?.toLowerCase().includes(query);
        const hasVideo =
            item.media?.some((media) => media.is_video) ||
            item.is_video ||
            item.cover_mime?.startsWith('video/');
        const hasPhoto = item.media?.length
            ? item.media.some((media) => !media.is_video)
            : Boolean(item.cover_url && !hasVideo);

        return (
            matchesSearch &&
            (mediaFilter.value === 'all' ||
                (mediaFilter.value === 'videos' ? hasVideo : hasPhoto))
        );
    });
});
function clearFilters(): void {
    searchQuery.value = '';
    mediaFilter.value = 'all';
}
</script>

<template>
    <SeoHead
        title="Moments | TopGrade London FC"
        description="Our Story. Their Journey. Matchdays, training, teams and the visual stories that define TopGrade London FC."
        path="/moments"
        :image="featured?.cover_url || undefined"
    />

    <div
        class="bg-tg-bg text-tg-text selection:bg-tg-accent min-h-screen selection:text-white"
    >
        <!-- 01 — HERO OPENING: Strong Featured Opening Moment -->
        <FeaturedMoment :featured="featured" />

        <!-- 02 — ARCHIVE HEADER & SEARCH -->
        <MomentFilters
            v-model="searchQuery"
            v-model:media-filter="mediaFilter"
            :count="filteredMoments.length"
        />

        <!-- 03 — EDITORIAL VISUAL ARCHIVE -->
        <MomentGallery
            :moments="filteredMoments"
            :search-query="
                searchQuery || (mediaFilter !== 'all' ? mediaFilter : '')
            "
            @clear-search="clearFilters"
        />

        <!-- 04 — BOTTOM CLUB SIGN-OFF (Quiet, Non-Competing) -->
        <div class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 sm:pb-24 lg:px-8">
            <section
                class="border-tg-border relative overflow-hidden rounded-xl border bg-black"
            >
                <div
                    class="pointer-events-none absolute inset-0 overflow-hidden"
                >
                    <img
                        src="/images/club/team-huddle.jpg"
                        alt=""
                        class="h-full w-full scale-[1.02] select-none object-cover object-right opacity-20 md:object-center"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 bg-black/90" />
                </div>

                <div class="relative z-10 max-w-xl p-6 sm:p-8 md:p-10">
                    <div
                        class="text-tg-accent mb-1.5 font-mono text-[10px] font-bold uppercase tracking-widest"
                    >
                        TOPGRADE LONDON FC
                    </div>
                    <h2
                        class="mb-2 text-xl font-normal uppercase tracking-tight text-white sm:text-2xl md:text-3xl"
                        style="font-family: var(--tg-display); line-height: 1"
                    >
                        Join TopGrade London FC.
                    </h2>
                    <p
                        class="mb-5 max-w-md text-xs leading-relaxed text-gray-300"
                    >
                        North and East London youth football built through the
                        game. Experience our coaching, culture, and competitive
                        matchdays.
                    </p>
                    <Link
                        href="/bookings"
                        class="tg-btn inline-flex items-center gap-2 px-5 py-2 text-xs shadow-sm"
                    >
                        <span>Book a Session</span>
                        <ArrowRight class="h-3.5 w-3.5" />
                    </Link>
                </div>
            </section>
        </div>
    </div>
</template>
