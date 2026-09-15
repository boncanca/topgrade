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

    <div class="min-h-screen bg-slate-950 text-white py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-12">
            <div class="space-y-4">
                <span class="inline-block px-3.5 py-1 rounded-full bg-slate-800 border border-slate-700 text-purple-300 text-xs font-semibold tracking-wider uppercase">
                    Editorial
                </span>
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">
                    Club News & Articles
                </h1>
                <p class="text-slate-400 text-base max-w-xl">
                    Official updates, team announcements, and matchday stories from TopGrade London FC.
                </p>
            </div>

            <!-- Articles Grid -->
            <div v-if="articles?.data && articles.data.length > 0" class="grid gap-6 md:grid-cols-3">
                <article
                    v-for="article in articles.data"
                    :key="article.id"
                    class="rounded-xl border border-slate-800 bg-slate-900 p-6 flex flex-col justify-between space-y-4"
                >
                    <div class="space-y-2">
                        <span v-if="article.published_at" class="text-xs text-slate-500 font-mono">
                            {{ new Date(article.published_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                        </span>
                        <h2 class="text-xl font-bold text-white leading-snug">
                            {{ article.title }}
                        </h2>
                        <p class="text-slate-400 text-sm line-clamp-3">
                            {{ article.excerpt }}
                        </p>
                    </div>

                    <div class="pt-2">
                        <Link
                            :href="`/articles/${article.slug}`"
                            class="text-sm font-semibold text-[var(--brand-secondary)] hover:underline"
                        >
                            Read Full Story →
                        </Link>
                    </div>
                </article>
            </div>

            <!-- Authentic Empty State -->
            <div v-else class="text-center py-20 bg-slate-900/50 rounded-2xl border border-slate-800/80 p-8 space-y-4 max-w-xl mx-auto">
                <Newspaper class="w-10 h-10 text-slate-600 mx-auto" />
                <h3 class="text-xl font-bold text-white">No Articles Published Yet</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Check back soon for upcoming club announcements, squad news, and training updates.
                </p>
                <div class="pt-2">
                    <Link href="/" class="text-sm text-[var(--brand-secondary)] hover:underline">
                        ← Return to Home
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
