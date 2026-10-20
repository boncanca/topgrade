<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    CalendarDays,
    Camera,
    ExternalLink,
    Maximize2,
    Play,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import MomentCard from '@/components/Public/Moments/MomentCard.vue';
import MomentViewer from '@/components/Public/Moments/MomentViewer.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface GalleryImage {
    id: number;
    url: string;
    name: string;
    is_cover: boolean;
    is_video?: boolean;
    mime_type?: string | null;
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
    people: { name: string; role: string | null; url: string | null }[];
    seo: { title: string; description: string; canonical_url: string | null };
}
interface RelatedMoment {
    id: number;
    title: string;
    slug: string;
    published_at?: string | null;
    cover_url: string | null;
    images_count: number;
}
const props = defineProps<{
    moment: MomentDetail;
    gallery: GalleryImage[];
    related: RelatedMoment[];
}>();
defineOptions({ layout: PublicLayout });
const viewer = ref<InstanceType<typeof MomentViewer> | null>(null);
const coverIndex = computed(() =>
    Math.max(
        0,
        props.gallery.findIndex((item) => item.is_cover),
    ),
);
const cover = computed(() => props.gallery[coverIndex.value]);
const hasVideos = computed(() => props.gallery.some((item) => item.is_video));
</script>

<template>
    <div class="bg-tg-bg text-tg-text min-h-screen">
        <SeoHead
            :title="moment.seo.title"
            :description="moment.seo.description"
            :path="`/moments/${moment.slug}`"
            :image="moment.cover_url || undefined"
        />
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <nav
                class="text-tg-text-muted flex items-center justify-between gap-4 text-xs"
                aria-label="Breadcrumb"
            >
                <Link
                    href="/moments"
                    class="tg-focus inline-flex items-center gap-2 hover:text-white"
                    ><ArrowLeft class="size-4" /> Back to Moments</Link
                >
                <span class="hidden truncate sm:block"
                    >Moments / {{ moment.title }}</span
                >
            </nav>
            <header
                class="border-tg-border grid items-center gap-8 border-b py-8 sm:py-10 lg:grid-cols-5 lg:gap-12"
            >
                <div class="lg:col-span-2">
                    <p
                        class="text-tg-accent mb-4 text-xs font-semibold uppercase tracking-widest"
                    >
                        Inside TopGrade
                    </p>
                    <h1
                        class="break-words text-4xl leading-none text-white sm:text-5xl xl:text-6xl"
                    >
                        {{ moment.title }}
                    </h1>
                    <p
                        v-if="moment.description"
                        class="text-tg-text-muted mt-5 whitespace-pre-line text-base leading-relaxed"
                    >
                        {{ moment.description }}
                    </p>
                    <div
                        class="text-tg-text-muted mt-6 flex flex-wrap gap-x-5 gap-y-3 text-xs"
                    >
                        <span
                            v-if="moment.published_at"
                            class="inline-flex items-center gap-2"
                            ><CalendarDays class="size-4" />{{
                                moment.published_at
                            }}</span
                        >
                        <span class="inline-flex items-center gap-2"
                            ><Camera class="size-4" />{{ gallery.length }}
                            {{
                                hasVideos
                                    ? 'items'
                                    : gallery.length === 1
                                      ? 'photo'
                                      : 'photos'
                            }}</span
                        >
                    </div>
                    <a
                        v-if="moment.external_link"
                        :href="moment.external_link"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="tg-btn ghost mt-6"
                        ><span>{{
                            moment.external_link_label || 'View details'
                        }}</span
                        ><ExternalLink class="size-4"
                    /></a>
                </div>
                <button
                    v-if="cover"
                    type="button"
                    class="aspect-16/10 border-tg-border focus-visible:outline-tg-accent group relative w-full overflow-hidden rounded-xl border bg-black focus-visible:outline-2 focus-visible:outline-offset-4 lg:col-span-3"
                    :aria-label="`Open ${moment.title} photo viewer`"
                    @click="viewer?.show(coverIndex, $event)"
                >
                    <img
                        v-if="!cover.is_video"
                        :src="cover.url"
                        :alt="cover.name || moment.title"
                        fetchpriority="high"
                        class="h-full w-full object-cover"
                    />
                    <span
                        v-else
                        class="text-tg-accent flex h-full items-center justify-center"
                        ><Play class="size-16"
                    /></span>
                    <span
                        class="absolute right-4 top-4 flex items-center gap-2 rounded border border-white/20 bg-black/80 px-3 py-2 text-xs text-white"
                        >View {{ hasVideos ? 'gallery' : 'photos'
                        }}<Maximize2 class="size-4"
                    /></span>
                    <span
                        class="absolute bottom-4 right-4 rounded bg-black/80 px-3 py-2 text-xs text-white"
                        >{{ String(coverIndex + 1).padStart(2, '0') }} /
                        {{ String(gallery.length).padStart(2, '0') }}</span
                    >
                </button>
            </header>
            <section
                v-if="gallery.length"
                class="py-6"
                aria-label="Album photos"
            >
                <p class="text-tg-text-muted mb-4 text-xs">
                    Select {{ hasVideos ? 'an item' : 'a photo' }} to view full
                    screen.
                </p>
                <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3">
                    <button
                        v-for="(item, index) in gallery"
                        :key="item.id"
                        type="button"
                        class="aspect-4/3 border-tg-border bg-tg-bg-deep focus-visible:outline-tg-accent group relative overflow-hidden rounded-lg border focus-visible:outline-2 focus-visible:outline-offset-4"
                        :aria-label="`Open ${item.is_video ? 'video' : 'photo'} ${index + 1}: ${item.name || moment.title}`"
                        @click="viewer?.show(index, $event)"
                    >
                        <span
                            v-if="item.is_video"
                            class="text-tg-accent flex h-full items-center justify-center"
                            ><Play class="size-10"
                        /></span>
                        <img
                            v-else
                            :src="item.url"
                            :alt="item.name || moment.title"
                            loading="lazy"
                            decoding="async"
                            class="h-full w-full object-cover motion-safe:transition-transform motion-safe:duration-500 motion-safe:group-hover:scale-105"
                        />
                        <span
                            class="absolute bottom-2 right-2 rounded bg-black/80 px-2 py-1 text-xs text-white"
                            >{{ String(index + 1).padStart(2, '0') }}</span
                        >
                    </button>
                </div>
                <p
                    v-if="moment.people?.length"
                    class="text-tg-text-muted mt-5 text-xs leading-relaxed"
                >
                    <span class="text-tg-accent font-semibold">Featuring </span
                    >{{
                        moment.people
                            .map((person) =>
                                person.role
                                    ? `${person.name} (${person.role})`
                                    : person.name,
                            )
                            .join(' · ')
                    }}
                </p>
            </section>
            <section
                v-else
                class="border-tg-border my-8 rounded-xl border border-dashed p-12 text-center"
            >
                <Camera class="text-tg-text-muted mx-auto mb-4 size-10" />
                <h2 class="mb-3 text-2xl text-white">Photos coming soon</h2>
                <p class="text-tg-text-muted text-sm">
                    We’re putting this album together. Check back soon.
                </p>
            </section>
            <section
                v-if="related.length"
                class="border-tg-border mt-8 border-t pb-10 pt-8"
                aria-label="Related albums"
            >
                <div class="mb-6 flex items-center justify-between gap-4">
                    <h2 class="text-2xl text-white">More moments</h2>
                    <Link
                        href="/moments"
                        class="tg-focus text-tg-accent inline-flex items-center gap-2 text-sm"
                        >View all<ArrowRight class="size-4"
                    /></Link>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <MomentCard
                        v-for="item in related"
                        :key="item.id"
                        :moment="item"
                    />
                </div>
            </section>
        </div>
        <MomentViewer ref="viewer" :gallery="gallery" :title="moment.title" />
    </div>
</template>
