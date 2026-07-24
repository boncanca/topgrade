<script setup lang="ts">
interface Payload {
    title: string;
    description?: string;
    link_text: string;
    link_url: string;
}

interface Settings {
    theme?: 'accent' | 'default';
}

const props = defineProps<{
    payload: Payload;
    settings?: Settings;
}>();
</script>

<template>
    <div class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div
                class="relative isolate overflow-hidden px-6 py-16 text-center shadow-2xl rounded-3xl sm:px-16"
                :class="{
                    'bg-slate-900 text-white dark:bg-slate-950': (props.settings?.theme ?? 'accent') === 'accent',
                    'bg-card text-card-foreground border border-border': props.settings?.theme === 'default',
                }"
            >
                <h2 class="mx-auto max-w-2xl text-3xl font-bold tracking-tight sm:text-4xl">
                    {{ props.payload.title }}
                </h2>
                <p v-if="props.payload.description" class="mx-auto mt-6 max-w-xl text-lg leading-8 opacity-80">
                    {{ props.payload.description }}
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a
                        :href="props.payload.link_url"
                        class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                        :class="{
                            'bg-primary text-primary-foreground hover:bg-primary/95': props.settings?.theme === 'default',
                        }"
                    >
                        {{ props.payload.link_text }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
