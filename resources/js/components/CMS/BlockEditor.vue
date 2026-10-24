<script setup lang="ts">
import { ref } from 'vue';
import draggable from 'vuedraggable';
import { generateUUID } from '@/lib/utils';
import { blockSchemas } from './blockSchemas';
import type { BlockFieldDefinition } from './blockSchemas';
import BlockTypePickerModal from './BlockTypePickerModal.vue';
import TiptapEditor from './TiptapEditor.vue';

export interface Block {
    uuid: string;
    type: string;
    payload: Record<string, any>;
    settings: Record<string, any>;
}

const props = defineProps<{
    modelValue: Block[];
    contentId?: number;
}>();

const emit = defineEmits<{
    'update:modelValue': [blocks: Block[]];
}>();

const pickerOpen = ref(false);
const collapsedBlocks = ref<Set<string>>(new Set());

function updateBlocks(newBlocks: Block[]) {
    emit('update:modelValue', newBlocks);
}

function addBlock(type: string) {
    const schema = blockSchemas[type];

    if (!schema) {
        return;
    }

    const newBlock: Block = {
        uuid: generateUUID(),
        type,
        payload: { ...schema.defaultPayload },
        settings: { ...(schema.defaultSettings ?? {}) },
    };

    emit('update:modelValue', [...props.modelValue, newBlock]);
}

function removeBlock(uuid: string) {
    emit(
        'update:modelValue',
        props.modelValue.filter((b) => b.uuid !== uuid),
    );
}

function toggleCollapse(uuid: string) {
    if (collapsedBlocks.value.has(uuid)) {
        collapsedBlocks.value.delete(uuid);
    } else {
        collapsedBlocks.value.add(uuid);
    }
}

function updateBlockField(
    uuid: string,
    section: 'payload' | 'settings',
    key: string,
    value: unknown,
) {
    const updated = props.modelValue.map((block) => {
        if (block.uuid !== uuid) {
            return block;
        }

        return {
            ...block,
            [section]: {
                ...block[section],
                [key]: value,
            },
        };
    });

    emit('update:modelValue', updated);
}

function updateRepeaterItem(
    uuid: string,
    fieldKey: string,
    index: number,
    subKey: string,
    value: unknown,
) {
    const updated = props.modelValue.map((block) => {
        if (block.uuid !== uuid) {
            return block;
        }

        const items = [
            ...((block.payload[fieldKey] as Record<string, unknown>[]) ?? []),
        ];
        items[index] = { ...items[index], [subKey]: value };

        return { ...block, payload: { ...block.payload, [fieldKey]: items } };
    });

    emit('update:modelValue', updated);
}

function addRepeaterItem(
    uuid: string,
    fieldKey: string,
    subFields: BlockFieldDefinition[],
) {
    const updated = props.modelValue.map((block) => {
        if (block.uuid !== uuid) {
            return block;
        }

        const empty = Object.fromEntries(subFields.map((f) => [f.key, '']));
        const items = [
            ...((block.payload[fieldKey] as Record<string, unknown>[]) ?? []),
            empty,
        ];

        return { ...block, payload: { ...block.payload, [fieldKey]: items } };
    });

    emit('update:modelValue', updated);
}

function removeRepeaterItem(uuid: string, fieldKey: string, index: number) {
    const updated = props.modelValue.map((block) => {
        if (block.uuid !== uuid) {
            return block;
        }

        const items = (
            (block.payload[fieldKey] as Record<string, unknown>[]) ?? []
        ).filter((_, i) => i !== index);

        return { ...block, payload: { ...block.payload, [fieldKey]: items } };
    });

    emit('update:modelValue', updated);
}

// ── Image upload via Spatie media library ───────────────────────────────────
const uploadingBlocks = ref<Set<string>>(new Set());

async function handleImageUpload(uuid: string, fieldKey: string, event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (!file) {
        return;
    }

    uploadingBlocks.value.add(`${uuid}-${fieldKey}`);

    const formData = new FormData();
    formData.append('file', file);

    if (props.contentId) {
        formData.append('content_id', String(props.contentId));
    }

    try {
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement
        )?.content;
        const response = await fetch('/dashboard/media/upload', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken ?? '' },
            body: formData,
        });

        if (!response.ok) {
            throw new Error('Upload failed');
        }

        const data = (await response.json()) as {
            uuid: string;
            url: string;
            id: number;
        };

        updateBlockField(uuid, 'payload', fieldKey, {
            url: data.url,
            mediaId: data.id,
            mediaUuid: data.uuid,
        });
    } catch {
        alert('Image upload failed. Please try a URL instead.');
    } finally {
        uploadingBlocks.value.delete(`${uuid}-${fieldKey}`);
    }
}

function getImageValue(
    block: Block,
    fieldKey: string,
): { url: string; mediaId: number | null } {
    const val = block.payload[fieldKey];

    if (val && typeof val === 'object' && 'url' in val) {
        return val as { url: string; mediaId: number | null };
    }

    return { url: String(val ?? ''), mediaId: null };
}

function setImageUrl(uuid: string, fieldKey: string, url: string) {
    const current = getImageValue(
        props.modelValue.find((b) => b.uuid === uuid)!,
        fieldKey,
    );
    updateBlockField(uuid, 'payload', fieldKey, { ...current, url });
}

function schemaFor(type: string) {
    return blockSchemas[type];
}
</script>

<template>
    <div class="space-y-4">
        <!-- Block Type Picker Modal -->
        <BlockTypePickerModal
            :open="pickerOpen"
            @close="pickerOpen = false"
            @select="addBlock"
        />

        <!-- Block List (draggable) -->
        <draggable
            :model-value="props.modelValue"
            item-key="uuid"
            handle=".drag-handle"
            @update:model-value="updateBlocks"
            class="space-y-3"
            ghost-class="opacity-40"
            animation="200"
        >
            <template #item="{ element: block }">
                <div
                    :key="block.uuid"
                    class="border-border bg-card shadow-xs overflow-hidden rounded-lg border"
                >
                    <!-- Block Header -->
                    <div
                        class="bg-muted/30 border-border flex items-center gap-3 border-b px-4 py-3"
                    >
                        <!-- Drag Handle -->
                        <button
                            type="button"
                            class="drag-handle text-muted-foreground hover:text-foreground cursor-grab touch-none transition-colors"
                            title="Drag to reorder"
                            aria-label="Drag to reorder block"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M8 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm8 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM8 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm8 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM8 22a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm8 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"
                                />
                            </svg>
                        </button>

                        <!-- Block Type Badge -->
                        <span
                            class="text-foreground flex-1 text-sm font-medium"
                        >
                            {{ schemaFor(block.type)?.icon ?? '📦' }}
                            {{ schemaFor(block.type)?.label ?? block.type }}
                        </span>

                        <!-- Actions -->
                        <div class="flex items-center gap-1">
                            <!-- Collapse / Expand -->
                            <button
                                type="button"
                                class="text-muted-foreground hover:text-foreground hover:bg-accent rounded p-1 transition-colors"
                                :title="
                                    collapsedBlocks.has(block.uuid)
                                        ? 'Expand'
                                        : 'Collapse'
                                "
                                :aria-label="
                                    collapsedBlocks.has(block.uuid)
                                        ? 'Expand block'
                                        : 'Collapse block'
                                "
                                @click="toggleCollapse(block.uuid)"
                            >
                                <svg
                                    class="h-4 w-4 transition-transform"
                                    :class="{
                                        'rotate-180': !collapsedBlocks.has(
                                            block.uuid,
                                        ),
                                    }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>

                            <!-- Delete -->
                            <button
                                type="button"
                                class="text-muted-foreground hover:text-destructive hover:bg-destructive/10 rounded p-1 transition-colors"
                                title="Remove block"
                                aria-label="Remove block"
                                @click="removeBlock(block.uuid)"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Block Fields -->
                    <div
                        v-if="!collapsedBlocks.has(block.uuid)"
                        class="space-y-6 p-4"
                    >
                        <!-- Payload Fields -->
                        <div
                            v-if="schemaFor(block.type)?.payloadFields?.length"
                            class="space-y-4"
                        >
                            <template
                                v-for="field in schemaFor(block.type)!
                                    .payloadFields"
                                :key="field.key"
                            >
                                <div>
                                    <label
                                        class="text-foreground mb-1.5 block text-sm font-medium"
                                    >
                                        {{ field.label }}
                                        <span
                                            v-if="field.required"
                                            class="text-destructive"
                                            >*</span
                                        >
                                    </label>

                                    <!-- Text -->
                                    <input
                                        v-if="field.type === 'text'"
                                        type="text"
                                        :value="
                                            block.payload[field.key] as string
                                        "
                                        :placeholder="field.placeholder"
                                        class="border-input bg-background shadow-xs focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border px-3 py-2 text-sm outline-none transition-[color,box-shadow] focus-visible:ring-[3px]"
                                        @input="
                                            updateBlockField(
                                                block.uuid,
                                                'payload',
                                                field.key,
                                                (
                                                    $event.target as HTMLInputElement
                                                ).value,
                                            )
                                        "
                                    />

                                    <!-- URL -->
                                    <input
                                        v-else-if="field.type === 'url'"
                                        type="url"
                                        :value="
                                            block.payload[field.key] as string
                                        "
                                        :placeholder="field.placeholder"
                                        class="border-input bg-background shadow-xs focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border px-3 py-2 text-sm outline-none transition-[color,box-shadow] focus-visible:ring-[3px]"
                                        @input="
                                            updateBlockField(
                                                block.uuid,
                                                'payload',
                                                field.key,
                                                (
                                                    $event.target as HTMLInputElement
                                                ).value,
                                            )
                                        "
                                    />

                                    <!-- Textarea -->
                                    <textarea
                                        v-else-if="field.type === 'textarea'"
                                        :value="
                                            block.payload[field.key] as string
                                        "
                                        :placeholder="field.placeholder"
                                        rows="4"
                                        class="border-input bg-background shadow-xs focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border px-3 py-2 text-sm outline-none transition-[color,box-shadow] focus-visible:ring-[3px]"
                                        @input="
                                            updateBlockField(
                                                block.uuid,
                                                'payload',
                                                field.key,
                                                (
                                                    $event.target as HTMLTextAreaElement
                                                ).value,
                                            )
                                        "
                                    />

                                    <!-- Rich Text (Tiptap) -->
                                    <TiptapEditor
                                        v-else-if="field.type === 'richtext'"
                                        :model-value="
                                            block.payload[field.key] as string
                                        "
                                        :placeholder="field.placeholder"
                                        @update:model-value="
                                            updateBlockField(
                                                block.uuid,
                                                'payload',
                                                field.key,
                                                $event,
                                            )
                                        "
                                    />

                                    <!-- Image (upload + URL) -->
                                    <div
                                        v-else-if="field.type === 'image'"
                                        class="space-y-3"
                                    >
                                        <!-- Upload -->
                                        <div class="flex items-center gap-3">
                                            <label
                                                class="border-input text-muted-foreground hover:border-primary hover:text-foreground cursor-pointer rounded-md border border-dashed px-4 py-2 text-sm transition-colors"
                                            >
                                                <span
                                                    v-if="
                                                        uploadingBlocks.has(
                                                            `${block.uuid}-${field.key}`,
                                                        )
                                                    "
                                                    >Uploading…</span
                                                >
                                                <span v-else
                                                    >📎 Upload image</span
                                                >
                                                <input
                                                    type="file"
                                                    accept="image/*"
                                                    class="sr-only"
                                                    :disabled="
                                                        uploadingBlocks.has(
                                                            `${block.uuid}-${field.key}`,
                                                        )
                                                    "
                                                    @change="
                                                        handleImageUpload(
                                                            block.uuid,
                                                            field.key,
                                                            $event,
                                                        )
                                                    "
                                                />
                                            </label>

                                            <span
                                                class="text-muted-foreground text-xs"
                                                >or enter a URL</span
                                            >
                                        </div>

                                        <!-- URL input -->
                                        <input
                                            type="url"
                                            :value="
                                                getImageValue(block, field.key)
                                                    .url
                                            "
                                            placeholder="https://example.com/image.jpg"
                                            class="border-input bg-background shadow-xs focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border px-3 py-2 text-sm outline-none transition-[color,box-shadow] focus-visible:ring-[3px]"
                                            @input="
                                                setImageUrl(
                                                    block.uuid,
                                                    field.key,
                                                    (
                                                        $event.target as HTMLInputElement
                                                    ).value,
                                                )
                                            "
                                        />

                                        <!-- Preview -->
                                        <img
                                            v-if="
                                                getImageValue(block, field.key)
                                                    .url
                                            "
                                            :src="
                                                getImageValue(block, field.key)
                                                    .url
                                            "
                                            :alt="
                                                (block.payload.alt as string) ||
                                                'Preview'
                                            "
                                            class="border-border max-h-40 rounded-md border object-cover"
                                        />
                                    </div>

                                    <!-- Repeater -->
                                    <div
                                        v-else-if="field.type === 'repeater'"
                                        class="space-y-3"
                                    >
                                        <div
                                            v-for="(item, idx) in block.payload[
                                                field.key
                                            ] as Record<string, unknown>[]"
                                            :key="idx"
                                            class="border-border bg-muted/20 flex items-start gap-2 rounded-md border p-3"
                                        >
                                            <div
                                                class="grid flex-1 grid-cols-2 gap-2"
                                            >
                                                <template
                                                    v-for="sf in field.subFields"
                                                    :key="sf.key"
                                                >
                                                    <input
                                                        type="text"
                                                        :value="
                                                            item[
                                                                sf.key
                                                            ] as string
                                                        "
                                                        :placeholder="
                                                            sf.placeholder ??
                                                            sf.label
                                                        "
                                                        class="border-input bg-background shadow-xs focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border px-3 py-2 text-sm outline-none transition-[color,box-shadow] focus-visible:ring-[3px]"
                                                        @input="
                                                            updateRepeaterItem(
                                                                block.uuid,
                                                                field.key,
                                                                idx,
                                                                sf.key,
                                                                (
                                                                    $event.target as HTMLInputElement
                                                                ).value,
                                                            )
                                                        "
                                                    />
                                                </template>
                                            </div>

                                            <button
                                                type="button"
                                                class="text-muted-foreground hover:text-destructive hover:bg-destructive/10 mt-2 rounded p-1 transition-colors"
                                                title="Remove item"
                                                @click="
                                                    removeRepeaterItem(
                                                        block.uuid,
                                                        field.key,
                                                        idx,
                                                    )
                                                "
                                            >
                                                ✕
                                            </button>
                                        </div>

                                        <button
                                            type="button"
                                            class="border-border text-muted-foreground hover:border-primary hover:text-foreground rounded-md border border-dashed px-4 py-2 text-sm transition-colors"
                                            @click="
                                                addRepeaterItem(
                                                    block.uuid,
                                                    field.key,
                                                    field.subFields ?? [],
                                                )
                                            "
                                        >
                                            + Add item
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Settings Fields -->
                        <div
                            v-if="schemaFor(block.type)?.settingsFields?.length"
                            class="border-border border-t pt-4"
                        >
                            <p
                                class="text-muted-foreground mb-3 text-xs font-semibold uppercase tracking-wide"
                            >
                                Display Settings
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <template
                                    v-for="field in schemaFor(block.type)!
                                        .settingsFields"
                                    :key="field.key"
                                >
                                    <div>
                                        <label
                                            class="text-foreground mb-1 block text-sm font-medium"
                                            >{{ field.label }}</label
                                        >

                                        <!-- Select -->
                                        <select
                                            v-if="field.type === 'select'"
                                            :value="
                                                block.settings[
                                                    field.key
                                                ] as string
                                            "
                                            class="border-input bg-background shadow-xs focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border px-3 py-2 text-sm outline-none transition-[color,box-shadow] focus-visible:ring-[3px]"
                                            @change="
                                                updateBlockField(
                                                    block.uuid,
                                                    'settings',
                                                    field.key,
                                                    (
                                                        $event.target as HTMLSelectElement
                                                    ).value,
                                                )
                                            "
                                        >
                                            <option
                                                v-for="opt in field.options"
                                                :key="opt.value"
                                                :value="opt.value"
                                            >
                                                {{ opt.label }}
                                            </option>
                                        </select>

                                        <!-- Toggle -->
                                        <label
                                            v-else-if="field.type === 'toggle'"
                                            class="flex cursor-pointer items-center gap-2"
                                        >
                                            <input
                                                type="checkbox"
                                                :checked="
                                                    block.settings[
                                                        field.key
                                                    ] as boolean
                                                "
                                                class="border-input rounded"
                                                @change="
                                                    updateBlockField(
                                                        block.uuid,
                                                        'settings',
                                                        field.key,
                                                        (
                                                            $event.target as HTMLInputElement
                                                        ).checked,
                                                    )
                                                "
                                            />
                                            <span
                                                class="text-foreground text-sm"
                                                >Enabled</span
                                            >
                                        </label>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </draggable>

        <!-- Empty state -->
        <div
            v-if="props.modelValue.length === 0"
            class="border-border rounded-lg border border-dashed py-12 text-center"
        >
            <p class="text-muted-foreground text-sm">
                No blocks yet. Add your first block below.
            </p>
        </div>

        <!-- Add Block Button -->
        <button
            id="add-block-btn"
            type="button"
            class="border-border text-muted-foreground hover:border-primary hover:text-foreground hover:bg-accent/50 w-full rounded-lg border border-dashed py-3 text-sm font-medium transition-colors"
            @click="pickerOpen = true"
        >
            + Add Block
        </button>
    </div>
</template>
