<script setup lang="ts">
import { Button } from '@/components/ui/button';

interface Props {
    title: string;
    description?: string;
    image?: string;
    price?: number;
    duration?: string;
    participants?: string;
    bookingUrl?: string;
    featured?: boolean;
    tags?: string[];
}

withDefaults(defineProps<Props>(), {
    featured: false,
});
</script>

<template>
    <div
        class="card-hover flex flex-col overflow-hidden"
        :class="{
            'ring-accent ring-2': featured,
        }"
    >
        <!-- Image -->
        <div
            v-if="image"
            class="bg-muted relative h-48 w-full overflow-hidden md:h-56"
        >
            <img
                :src="image"
                :alt="title"
                class="h-full w-full object-cover transition-transform group-hover:scale-105"
            />
            <div v-if="featured" class="absolute inset-0 bg-black/40" />
        </div>

        <!-- Tags -->
        <div v-if="tags?.length" class="flex flex-wrap gap-2 px-4 pt-4">
            <span v-for="tag in tags" :key="tag" class="badge-primary text-xs">
                {{ tag }}
            </span>
        </div>

        <!-- Content -->
        <div class="flex flex-1 flex-col px-4 py-4">
            <h3 class="text-foreground text-xl font-semibold">{{ title }}</h3>

            <p
                v-if="description"
                class="text-muted-foreground mt-2 flex-1 text-sm"
            >
                {{ description }}
            </p>

            <!-- Details -->
            <div class="my-4 space-y-2 text-sm">
                <div
                    v-if="duration"
                    class="text-foreground flex items-center gap-2"
                >
                    <i class="las la-clock text-accent" />
                    <span>{{ duration }}</span>
                </div>
                <div
                    v-if="participants"
                    class="text-foreground flex items-center gap-2"
                >
                    <i class="las la-users text-accent" />
                    <span>{{ participants }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-border border-t px-4 py-4">
            <div class="flex items-center justify-between gap-4">
                <div v-if="price" class="text-primary text-2xl font-bold">
                    ${{ price }}
                </div>
                <Button
                    v-if="bookingUrl"
                    as-child
                    :variant="featured ? 'default' : 'outline'"
                    size="sm"
                >
                    <a :href="bookingUrl"> Book Now </a>
                </Button>
            </div>
        </div>
    </div>
</template>
