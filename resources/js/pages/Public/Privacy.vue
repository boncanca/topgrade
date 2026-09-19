<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import BlockRenderer from '@/components/CMS/BlockRenderer.vue';

interface Block {
    id: number;
    uuid: string;
    type: string;
    payload: any;
    settings?: any;
}

interface PageContent {
    id: number;
    title: string;
    slug: string;
    content?: string;
    blocks?: Block[];
}

defineProps<{
    page?: PageContent | null;
}>();

defineOptions({
    layout: PublicLayout,
});
</script>

<template>
    <SeoHead
        title="Privacy Policy | TopGrade London FC"
        description="TopGrade London FC privacy policy detailing how we collect, protect, and process parent, guardian, and youth player information under UK GDPR."
        path="/privacy-policy"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-10">
            <div>
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase mb-4">
                    Data Protection &amp; Governance
                </span>
                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    {{ page?.title || 'Privacy Policy' }}
                </h1>
                <p class="text-tg-text-muted text-sm mt-2">Last updated: September 2026 · UK GDPR &amp; Data Protection Act 2018</p>
            </div>

            <div class="rounded-xs border border-tg-border bg-tg-bg-deep/80 p-6 sm:p-10 space-y-8 text-sm sm:text-base leading-relaxed">
                <!-- Volara CMS Block Editor Renderer -->
                <div v-if="page?.blocks && page.blocks.length > 0">
                    <BlockRenderer :blocks="page.blocks" />
                </div>

                <!-- Volara CMS Published Content -->
                <div
                    v-else-if="page?.content"
                    class="space-y-6 text-tg-text legal-cms-content"
                    v-html="page.content"
                />
            </div>

            <div class="pt-4 border-t border-tg-border flex items-center justify-between">
                <Link href="/" class="tg-link inline-flex items-center gap-1.5 text-xs">
                    ← Return to Home
                </Link>
                <Link href="/terms-and-conditions" class="tg-link inline-flex items-center gap-1.5 text-xs">
                    Terms &amp; Conditions →
                </Link>
            </div>
        </div>
    </div>
</template>
