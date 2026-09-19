<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    schema: Record<string, any>;
}>();
</script>

<template>
    <Head>
        <component :is="'script'" type="application/ld+json">
            {{ JSON.stringify(schema) }}
        </component>
    </Head>
</template>
