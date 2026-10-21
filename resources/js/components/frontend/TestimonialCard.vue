<script setup lang="ts">
interface Props {
    name: string;
    role?: string;
    content: string;
    image?: string;
    rating?: number;
}

defineProps<Props>();
</script>

<template>
    <div class="card-hover flex flex-col">
        <!-- Rating -->
        <div v-if="rating" class="mb-4 flex gap-1">
            <i
                v-for="i in 5"
                :key="i"
                :class="{
                    'las la-star text-accent text-lg': i <= rating,
                    'las la-star text-border text-lg': i > rating,
                }"
            />
        </div>

        <!-- Content -->
        <p class="text-foreground mb-6 flex-1">"{{ content }}"</p>

        <!-- Author -->
        <div class="border-border flex items-center gap-4 border-t pt-4">
            <div
                v-if="image"
                class="bg-muted h-12 w-12 flex-shrink-0 overflow-hidden rounded-full"
            >
                <img
                    :src="image"
                    :alt="name"
                    class="h-full w-full object-cover"
                />
            </div>

            <div class="flex-1">
                <p class="text-foreground font-semibold">{{ name }}</p>
                <p v-if="role" class="text-muted-foreground text-sm">
                    {{ role }}
                </p>
            </div>
        </div>
    </div>
</template>
