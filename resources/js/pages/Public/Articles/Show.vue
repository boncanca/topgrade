<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import { ArrowLeft, ArrowRight } from '@lucide/vue';

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

    <div class="min-h-screen bg-tg-bg text-tg-text py-16 sm:py-24 px-4 sm:px-6 lg:px-8">
        <article class="max-w-4xl mx-auto space-y-8">
            <div>
                <Link href="/articles" class="tg-btn ghost inline-flex items-center gap-2 text-xs py-1.5 px-3">
                    <ArrowLeft class="w-3.5 h-3.5" />
                    <span>Back to All Articles</span>
                </Link>
            </div>

            <header class="space-y-4">
                <span v-if="article.published_at" class="text-xs text-tg-accent font-mono block">
                    {{ new Date(article.published_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                </span>
                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong leading-tight"
                    style="font-family: var(--tg-display);"
                >
                    {{ article.title }}
                </h1>
                <p v-if="article.excerpt" class="text-tg-text text-lg sm:text-xl leading-relaxed border-l-2 border-tg-accent pl-4">
                    {{ article.excerpt }}
                </p>
            </header>

            <div class="rounded-xs border border-tg-border bg-tg-bg-deep/80 p-6 sm:p-10 text-tg-text text-base leading-relaxed space-y-6" v-html="article.content" />

            <div class="pt-8 border-t border-tg-border flex flex-col sm:flex-row justify-between items-center gap-4">
                <Link href="/articles" class="tg-btn ghost text-xs py-2.5 px-4 w-full sm:w-auto text-center">
                    ← Back to Club News
                </Link>
                <Link href="/bookings/free-trial-session" class="tg-btn text-xs py-2.5 px-4 w-full sm:w-auto justify-center">
                    <span>Book a Trial</span>
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </article>
    </div>
</template>
