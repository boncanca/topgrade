<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ArrowLeft } from '@lucide/vue';

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
    <Head :title="`${article.title} — TopGrade London FC`" />

    <div class="min-h-screen bg-slate-950 text-white py-20 px-4 sm:px-6 lg:px-8">
        <article class="max-w-3xl mx-auto space-y-8">
            <Link href="/articles" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
                <ArrowLeft class="w-4 h-4" />
                <span>Back to All Articles</span>
            </Link>

            <header class="space-y-4">
                <span v-if="article.published_at" class="text-xs text-purple-400 font-mono">
                    {{ new Date(article.published_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                </span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight">
                    {{ article.title }}
                </h1>
                <p v-if="article.excerpt" class="text-slate-300 text-lg sm:text-xl leading-relaxed">
                    {{ article.excerpt }}
                </p>
            </header>

            <div class="prose prose-invert prose-slate max-w-none text-slate-300 text-base leading-relaxed pt-6 border-t border-slate-800" v-html="article.content" />

            <div class="pt-12 border-t border-slate-800 flex justify-between items-center">
                <Link href="/articles" class="text-sm text-slate-400 hover:text-white transition-colors">
                    ← Back to Club News
                </Link>
                <Link href="/bookings" class="px-5 py-2.5 bg-[var(--brand-primary)] hover:opacity-90 text-white text-sm font-semibold rounded-lg transition-opacity">
                    Book a Trial
                </Link>
            </div>
        </article>
    </div>
</template>
