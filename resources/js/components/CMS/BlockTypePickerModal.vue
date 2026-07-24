<script setup lang="ts">
import { blockTypeList } from './blockSchemas';

defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    close: [];
    select: [type: string];
}>();
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Add a block"
        >
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                @click="emit('close')"
            />

            <!-- Modal -->
            <div class="relative z-10 w-full max-w-lg rounded-xl border border-border bg-background shadow-2xl">
                <div class="flex items-center justify-between border-b border-border px-5 py-4">
                    <div>
                        <h2 class="text-base font-semibold text-foreground">Add a Block</h2>
                        <p class="text-sm text-muted-foreground">Choose a block type to insert</p>
                    </div>
                    <button
                        type="button"
                        class="rounded-md p-1.5 text-muted-foreground hover:bg-accent hover:text-foreground transition-colors"
                        aria-label="Close"
                        @click="emit('close')"
                    >
                        ✕
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-3 p-5">
                    <button
                        v-for="block in blockTypeList"
                        :key="block.type"
                        type="button"
                        :id="`block-type-${block.type}`"
                        class="flex items-start gap-3 rounded-lg border border-border p-3 text-left transition-colors hover:bg-accent hover:border-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        @click="emit('select', block.type); emit('close')"
                    >
                        <span class="text-2xl leading-none mt-0.5" aria-hidden="true">{{ block.icon }}</span>
                        <span>
                            <span class="block text-sm font-medium text-foreground">{{ block.label }}</span>
                            <span class="block text-xs text-muted-foreground mt-0.5 leading-snug">{{ block.description }}</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
