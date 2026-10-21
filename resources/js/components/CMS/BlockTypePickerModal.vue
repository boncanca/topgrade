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
            <div
                class="border-border bg-background relative z-10 w-full max-w-lg rounded-xl border shadow-2xl"
            >
                <div
                    class="border-border flex items-center justify-between border-b px-5 py-4"
                >
                    <div>
                        <h2 class="text-foreground text-base font-semibold">
                            Add a Block
                        </h2>
                        <p class="text-muted-foreground text-sm">
                            Choose a block type to insert
                        </p>
                    </div>
                    <button
                        type="button"
                        class="text-muted-foreground hover:bg-accent hover:text-foreground rounded-md p-1.5 transition-colors"
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
                        class="border-border hover:bg-accent hover:border-primary focus-visible:ring-ring flex items-start gap-3 rounded-lg border p-3 text-left transition-colors focus-visible:outline-none focus-visible:ring-2"
                        @click="
                            emit('select', block.type);
                            emit('close');
                        "
                    >
                        <span
                            class="mt-0.5 text-2xl leading-none"
                            aria-hidden="true"
                            >{{ block.icon }}</span
                        >
                        <span>
                            <span
                                class="text-foreground block text-sm font-medium"
                                >{{ block.label }}</span
                            >
                            <span
                                class="text-muted-foreground mt-0.5 block text-xs leading-snug"
                                >{{ block.description }}</span
                            >
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
