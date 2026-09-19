<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useSeo, type SeoOptions } from '@/composables/useSeo';

const props = withDefaults(defineProps<SeoOptions>(), {
    path: '',
    image: '/images/og/topgrade-london-fc.jpg',
    type: 'website',
    noindex: false,
});

const { title, description, canonicalUrl, ogImageUrl, robotsContent, type } = useSeo(props);
</script>

<template>
    <Head :title="title">
        <meta name="description" :content="description" />
        <link rel="canonical" :href="canonicalUrl" />
        <meta name="robots" :content="robotsContent" />

        <!-- Open Graph / Facebook -->
        <meta property="og:type" :content="type" />
        <meta property="og:site_name" content="TopGrade London FC" />
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta property="og:image" :content="ogImageUrl" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" :content="ogImageUrl" />

        <slot />
    </Head>
</template>
