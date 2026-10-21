<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import BlockRenderer from '@/components/CMS/BlockRenderer.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

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

    <div class="bg-tg-bg text-tg-text min-h-screen px-4 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl space-y-10">
            <div>
                <span
                    class="rounded-xs bg-tg-bg-deep border-tg-border text-tg-accent mb-4 inline-block border px-3 py-1 text-xs font-bold uppercase tracking-widest"
                >
                    Data Protection &amp; Governance
                </span>
                <h1
                    class="text-tg-text-strong text-3xl font-normal uppercase tracking-tight sm:text-5xl"
                    style="font-family: var(--tg-display)"
                >
                    {{ page?.title || 'Privacy Policy' }}
                </h1>
                <p class="text-tg-text-muted mt-2 text-sm">
                    Last updated: September 2026 · UK GDPR &amp; Data Protection
                    Act 2018
                </p>
            </div>

            <div
                class="rounded-xs border-tg-border bg-tg-bg-deep/80 space-y-8 border p-6 text-sm leading-relaxed sm:p-10 sm:text-base"
            >
                <!-- Volara CMS Block Editor Renderer -->
                <div v-if="page?.blocks && page.blocks.length > 0">
                    <BlockRenderer :blocks="page.blocks" />
                </div>

                <!-- Volara CMS Published Content -->
                <div
                    v-else-if="page?.content"
                    class="text-tg-text legal-cms-content space-y-6"
                    v-html="page.content"
                />
            </div>

            <div
                class="border-tg-border flex items-center justify-between border-t pt-4"
            >
                <Link
                    href="/"
                    class="tg-link inline-flex items-center gap-1.5 text-xs"
                >
                    ← Return to Home
                </Link>
                <Link
                    href="/terms-and-conditions"
                    class="tg-link inline-flex items-center gap-1.5 text-xs"
                >
                    Terms &amp; Conditions →
                </Link>
            </div>
        </div>
    </div>
</template>
