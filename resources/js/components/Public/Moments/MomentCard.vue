<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Camera, Play } from '@lucide/vue';

interface MomentItem {
    id: number;
    title: string;
    slug: string;
    description?: string | null;
    published_at?: string | null;
    images_count: number;
    cover_url: string | null;
    cover_mime?: string | null;
    is_video?: boolean;
    media?: { is_video?: boolean }[];
}

defineProps<{ moment: MomentItem }>();
</script>

<template>
    <Link
        :href="`/moments/${moment.slug}`"
        class="border-tg-border bg-tg-bg-deep hover:border-tg-accent focus-visible:outline-tg-accent group flex h-full flex-col overflow-hidden rounded-xl border transition-colors focus-visible:outline-2 focus-visible:outline-offset-4"
    >
        <div
            class="relative h-60 shrink-0 overflow-hidden bg-black sm:h-64 lg:h-72"
        >
            <img
                v-if="
                    moment.cover_url &&
                    !moment.cover_mime?.startsWith('video/') &&
                    !moment.is_video
                "
                :src="moment.cover_url"
                :alt="moment.title"
                loading="lazy"
                decoding="async"
                class="h-full w-full object-cover motion-safe:transition-transform motion-safe:duration-500 motion-safe:group-hover:scale-105"
            />
            <div
                v-else
                class="text-tg-text-muted flex h-full items-center justify-center"
            >
                <Play
                    v-if="
                        moment.cover_mime?.startsWith('video/') ||
                        moment.is_video
                    "
                    class="size-10"
                />
                <Camera v-else class="size-10" />
            </div>
            <span
                v-if="
                    moment.media?.some((item) => item.is_video) ||
                    moment.is_video ||
                    moment.cover_mime?.startsWith('video/')
                "
                class="absolute left-3 top-3 flex items-center gap-1.5 rounded border border-white/20 bg-black/80 px-2 py-1 text-xs text-white"
            >
                <Play class="text-tg-accent size-3" /> Video
            </span>
        </div>
        <div class="flex grow flex-col gap-3 p-4 sm:p-5">
            <h3
                class="group-hover:text-tg-accent text-xl leading-tight text-white transition-colors"
            >
                {{ moment.title }}
            </h3>
            <div
                class="border-tg-border text-tg-text-muted mt-auto flex items-center justify-between gap-3 border-t pt-3 text-xs"
            >
                <div class="flex flex-wrap gap-x-3 gap-y-1">
                    <span v-if="moment.published_at">{{
                        moment.published_at
                    }}</span>
                    <span
                        >{{ moment.images_count }}
                        {{
                            moment.media?.some((item) => item.is_video) ||
                            moment.is_video ||
                            moment.cover_mime?.startsWith('video/')
                                ? 'items'
                                : moment.images_count === 1
                                  ? 'photo'
                                  : 'photos'
                        }}</span
                    >
                </div>
                <ArrowRight
                    class="size-4 shrink-0 text-white"
                    aria-hidden="true"
                />
            </div>
        </div>
    </Link>
</template>
