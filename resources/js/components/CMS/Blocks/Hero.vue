<script setup lang="ts">
interface Payload {
    title: string;
    subtitle?: string;
    button_text?: string;
    button_url?: string;
}

interface Settings {
    theme?: 'light' | 'dark' | 'glass';
    align?: 'left' | 'center' | 'right';
}

const props = defineProps<{
    payload: Payload;
    settings?: Settings;
}>();
</script>

<template>
    <div
        class="relative overflow-hidden py-24 sm:py-32 rounded-2xl border border-border"
        :class="{
            'bg-background text-foreground': (props.settings?.theme ?? 'light') === 'light',
            'bg-card text-card-foreground shadow-xl': props.settings?.theme === 'glass',
            'bg-primary text-primary-foreground': props.settings?.theme === 'dark',
        }"
    >
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div
                class="mx-auto max-w-2xl"
                :class="{
                    'text-left': (props.settings?.align ?? 'center') === 'left',
                    'text-center': (props.settings?.align ?? 'center') === 'center',
                    'text-right': (props.settings?.align ?? 'center') === 'right',
                }"
            >
                <h1 class="text-4xl font-semibold tracking-tight sm:text-6xl">
                    {{ props.payload.title }}
                </h1>
                <p v-if="props.payload.subtitle" class="mt-6 text-lg leading-8 opacity-80">
                    {{ props.payload.subtitle }}
                </p>
                <div v-if="props.payload.button_text && props.payload.button_url" class="mt-10 flex items-center justify-center gap-x-6">
                    <a
                        :href="props.payload.button_url"
                        class="rounded-md px-3.5 py-2.5 text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                        :class="{
                            'bg-primary text-primary-foreground hover:bg-primary/90': (props.settings?.theme ?? 'light') !== 'dark',
                            'bg-background text-foreground hover:bg-background/90': props.settings?.theme === 'dark',
                        }"
                    >
                        {{ props.payload.button_text }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
