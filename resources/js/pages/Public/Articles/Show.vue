<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight } from '@lucide/vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface Article {
    id: number;
    title: string;
    slug: string;
    excerpt?: string;
    content: string;
    published_at?: string;
}

defineProps<{
    article: Article;
}>();

defineOptions({
    layout: PublicLayout,
});
</script>

<template>
    <SeoHead
        :title="`${article.title} | TopGrade London FC`"
        :description="article.excerpt || article.title"
        :path="`/articles/${article.slug}`"
        type="article"
    />

    <div
        class="bg-tg-bg text-tg-text min-h-screen px-4 py-16 sm:px-6 sm:py-24 lg:px-8"
    >
        <article class="mx-auto max-w-4xl space-y-8">
            <div>
                <Link
                    href="/articles"
                    class="tg-btn ghost inline-flex items-center gap-2 px-3 py-1.5 text-xs"
                >
                    <ArrowLeft class="h-3.5 w-3.5" />
                    <span>Back to All Articles</span>
                </Link>
            </div>

            <header class="space-y-4">
                <span
                    v-if="article.published_at"
                    class="text-tg-accent block font-mono text-xs"
                >
                    {{
                        new Date(article.published_at).toLocaleDateString(
                            'en-GB',
                            { day: 'numeric', month: 'long', year: 'numeric' },
                        )
                    }}
                </span>
                <h1
                    class="text-tg-text-strong text-3xl font-normal uppercase leading-tight tracking-tight sm:text-5xl"
                    style="font-family: var(--tg-display)"
                >
                    {{ article.title }}
                </h1>
                <p
                    v-if="article.excerpt"
                    class="text-tg-text border-tg-accent border-l-2 pl-4 text-lg leading-relaxed sm:text-xl"
                >
                    {{ article.excerpt }}
                </p>
            </header>

            <div
                class="rounded-xs border-tg-border bg-tg-bg-deep/80 text-tg-text space-y-6 border p-6 text-base leading-relaxed sm:p-10"
                v-html="article.content"
            />

            <div
                class="border-tg-border flex flex-col items-center justify-between gap-4 border-t pt-8 sm:flex-row"
            >
                <Link
                    href="/articles"
                    class="tg-btn ghost w-full px-4 py-2.5 text-center text-xs sm:w-auto"
                >
                    ← Back to Club News
                </Link>
                <Link
                    href="/bookings/free-trial-session"
                    class="tg-btn w-full justify-center px-4 py-2.5 text-xs sm:w-auto"
                >
                    <span>Book a Trial</span>
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
        </article>
    </div>
</template>
