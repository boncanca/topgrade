<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaLinkProps } from '@inertiajs/vue3';

interface Breadcrumb {
    label?: string;
    title?: string;
    href?: NonNullable<InertiaLinkProps['href']>;
}

interface Props {
    items: Breadcrumb[];
}

withDefaults(defineProps<Props>(), {});
</script>

<template>
    <nav
        v-if="items.length > 0"
        class="text-muted-foreground flex items-center gap-2 text-sm"
    >
        <template v-for="(item, index) in items" :key="index">
            <Link
                v-if="item.href"
                :href="item.href"
                class="hover:text-foreground transition-colors"
            >
                {{ item.label ?? item.title }}
            </Link>
            <span v-else class="text-foreground font-medium">
                {{ item.label ?? item.title }}
            </span>
            <span
                v-if="index < items.length - 1"
                class="text-muted-foreground/60"
                >/</span
            >
        </template>
    </nav>
</template>
