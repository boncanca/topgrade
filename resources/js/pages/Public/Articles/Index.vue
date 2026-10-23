<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Newspaper } from '@lucide/vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

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
    <SeoHead
        title="News & Articles | TopGrade London FC"
        description="Latest club news, squad announcements, training updates, and matchday reports from TopGrade London FC in North and East London."
        path="/articles"
    />

    <div class="bg-tg-bg text-tg-text min-h-screen">
        <!-- Hero Section -->
        <section
            class="border-tg-border relative flex min-h-[36vh] items-end overflow-hidden border-b pb-12 pt-24 sm:min-h-[40vh] sm:pt-28"
        >
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="
                    background-image: url('/images/club/youth-squad-team-topgrade-london-fc.webp');
                "
            />
            <div
                class="from-tg-bg via-tg-bg/85 absolute inset-0 bg-gradient-to-t to-transparent"
            />

            <div
                class="relative z-10 mx-auto w-full max-w-7xl space-y-3 px-4 sm:px-6 lg:px-8"
            >
                <span
                    class="rounded-xs bg-tg-bg-deep border-tg-border text-tg-accent inline-block border px-3 py-1 text-xs font-bold uppercase tracking-widest"
                >
                    Club Editorial
                </span>

                <h1
                    class="text-tg-text-strong text-3xl font-normal uppercase tracking-tight sm:text-5xl"
                    style="font-family: var(--tg-display)"
                >
                    Club News &amp; Articles
                </h1>
                <p class="text-tg-text max-w-xl text-sm sm:text-base">
                    Official announcements, squad reports, and matchday stories
                    from TopGrade London FC.
                </p>
            </div>
        </section>

        <!-- Main Content -->
        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <!-- Articles Grid -->
            <div
                v-if="articles?.data && articles.data.length > 0"
                class="grid gap-6 md:grid-cols-3"
            >
                <article
                    v-for="article in articles.data"
                    :key="article.id"
                    class="rounded-xs border-tg-border bg-tg-bg-deep/80 hover:border-tg-border-strong flex flex-col justify-between space-y-5 border p-6 transition-colors"
                >
                    <div class="space-y-3">
                        <span
                            v-if="article.published_at"
                            class="text-tg-accent block font-mono text-xs"
                        >
                            {{
                                new Date(
                                    article.published_at,
                                ).toLocaleDateString('en-GB', {
                                    day: 'numeric',
                                    month: 'short',
                                    year: 'numeric',
                                })
                            }}
                        </span>
                        <h2
                            class="text-tg-text-strong text-xl font-normal uppercase leading-snug"
                            style="font-family: var(--tg-display)"
                        >
                            {{ article.title }}
                        </h2>
                        <p
                            class="text-tg-text-muted line-clamp-3 text-sm leading-relaxed"
                        >
                            {{ article.excerpt }}
                        </p>
                    </div>

                    <div class="border-tg-border/50 border-t pt-4">
                        <Link
                            :href="`/articles/${article.slug}`"
                            class="tg-link text-tg-accent hover:text-tg-accent-hover inline-flex items-center gap-1.5 text-xs"
                        >
                            <span>Read Full Story</span>
                            <span>→</span>
                        </Link>
                    </div>
                </article>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="bg-tg-bg-deep/80 rounded-xs border-tg-border mx-auto max-w-xl space-y-4 border p-8 py-16 text-center sm:p-12"
            >
                <div
                    class="bg-tg-accent/15 border-tg-accent/30 mx-auto flex h-12 w-12 items-center justify-center rounded-full border"
                >
                    <Newspaper class="text-tg-accent h-6 w-6" />
                </div>
                <h3
                    class="text-tg-text-strong text-2xl font-normal uppercase"
                    style="font-family: var(--tg-display)"
                >
                    No Articles Published Yet
                </h3>
                <p class="text-tg-text-muted text-sm leading-relaxed">
                    Check back soon for upcoming club announcements, squad
                    fixtures, and club news.
                </p>
                <div class="pt-2">
                    <Link href="/" class="tg-btn ghost px-4 py-2 text-xs">
                        ← Return to Home
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
