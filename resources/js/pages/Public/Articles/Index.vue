<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Newspaper } from '@lucide/vue';

interface Article {
    id: number;
    title: string;
    slug: string;
    excerpt?: string;
    published_at?: string;
}

interface Pagination {
    data: Article[];
    current_page: number;
    last_page: number;
    total: number;
}

defineProps<{
    articles: Pagination;
}>();

defineOptions({
    layout: PublicLayout,
});
</script>

<template>
    <Head title="Club News & Articles — TopGrade London FC" />

    <div class="min-h-screen bg-tg-bg text-tg-text">
        <!-- Hero Section -->
        <section class="relative min-h-[36vh] sm:min-h-[40vh] flex items-end overflow-hidden pb-12 pt-24 sm:pt-28 border-b border-tg-border">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="background-image: url('/images/club/club-training-london.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-tg-bg via-tg-bg/85 to-transparent" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase">
                    Club Editorial
                </span>

                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    Club News &amp; Articles
                </h1>
                <p class="text-tg-text text-sm sm:text-base max-w-xl">
                    Official announcements, squad reports, and matchday stories from TopGrade London FC.
                </p>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-14 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <!-- Articles Grid -->
            <div v-if="articles?.data && articles.data.length > 0" class="grid gap-6 md:grid-cols-3">
                <article
                    v-for="article in articles.data"
                    :key="article.id"
                    class="rounded-xs border border-tg-border bg-tg-bg-deep/80 hover:border-tg-border-strong p-6 flex flex-col justify-between space-y-5 transition-colors"
                >
                    <div class="space-y-3">
                        <span v-if="article.published_at" class="text-xs text-tg-accent font-mono block">
                            {{ new Date(article.published_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                        </span>
                        <h2
                            class="text-xl font-normal uppercase text-tg-text-strong leading-snug"
                            style="font-family: var(--tg-display);"
                        >
                            {{ article.title }}
                        </h2>
                        <p class="text-tg-text-muted text-sm line-clamp-3 leading-relaxed">
                            {{ article.excerpt }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-tg-border/50">
                        <Link
                            :href="`/articles/${article.slug}`"
                            class="tg-link inline-flex items-center gap-1.5 text-xs text-tg-accent hover:text-tg-accent-hover"
                        >
                            <span>Read Full Story</span>
                            <span>→</span>
                        </Link>
                    </div>
                </article>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16 bg-tg-bg-deep/80 rounded-xs border border-tg-border p-8 sm:p-12 space-y-4 max-w-xl mx-auto">
                <div class="w-12 h-12 rounded-full bg-tg-accent/15 border border-tg-accent/30 flex items-center justify-center mx-auto">
                    <Newspaper class="w-6 h-6 text-tg-accent" />
                </div>
                <h3
                    class="text-2xl font-normal uppercase text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    No Articles Published Yet
                </h3>
                <p class="text-tg-text-muted text-sm leading-relaxed">
                    Check back soon for upcoming club announcements, squad fixtures, and academy news.
                </p>
                <div class="pt-2">
                    <Link href="/" class="tg-btn ghost text-xs py-2 px-4">
                        ← Return to Home
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
