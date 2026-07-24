<script setup lang="ts">
import { computed, onBeforeUnmount, watch } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Link.configure({ openOnClick: false }),
    ],
    editorProps: {
        attributes: {
            class: 'prose prose-sm dark:prose-invert max-w-none focus:outline-none min-h-32 p-3',
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

// Sync external value changes (e.g. when parent resets the form)
watch(
    () => props.modelValue,
    (value) => {
        if (editor.value && editor.value.getHTML() !== value) {
            editor.value.commands.setContent(value ?? '', false);
        }
    },
);

const toolbarActions = computed(() => {
    const e = editor.value;
    if (!e) {
        return [];
    }

    return [
        { label: 'Bold', icon: 'B', isActive: () => e.isActive('bold'), command: () => e.chain().focus().toggleBold().run() },
        { label: 'Italic', icon: 'I', isActive: () => e.isActive('italic'), command: () => e.chain().focus().toggleItalic().run() },
        { label: 'Strike', icon: 'S̶', isActive: () => e.isActive('strike'), command: () => e.chain().focus().toggleStrike().run() },
        { label: 'H2', icon: 'H2', isActive: () => e.isActive('heading', { level: 2 }), command: () => e.chain().focus().toggleHeading({ level: 2 }).run() },
        { label: 'H3', icon: 'H3', isActive: () => e.isActive('heading', { level: 3 }), command: () => e.chain().focus().toggleHeading({ level: 3 }).run() },
        { label: 'Bullet List', icon: '•—', isActive: () => e.isActive('bulletList'), command: () => e.chain().focus().toggleBulletList().run() },
        { label: 'Ordered List', icon: '1.', isActive: () => e.isActive('orderedList'), command: () => e.chain().focus().toggleOrderedList().run() },
        { label: 'Blockquote', icon: '❝', isActive: () => e.isActive('blockquote'), command: () => e.chain().focus().toggleBlockquote().run() },
        { label: 'Undo', icon: '↩', isActive: () => false, command: () => e.chain().focus().undo().run() },
        { label: 'Redo', icon: '↪', isActive: () => false, command: () => e.chain().focus().redo().run() },
    ];
});

onBeforeUnmount(() => {
    editor.value?.destroy();
});
</script>

<template>
    <div class="rounded-md border border-input bg-background shadow-xs overflow-hidden focus-within:border-ring focus-within:ring-ring/50 focus-within:ring-[3px] transition-[color,box-shadow]">
        <!-- Toolbar -->
        <div v-if="editor" class="flex flex-wrap gap-1 border-b border-border px-2 py-1.5 bg-muted/40">
            <button
                v-for="action in toolbarActions"
                :key="action.label"
                type="button"
                :title="action.label"
                :class="[
                    'rounded px-2 py-1 text-xs font-medium transition-colors',
                    action.isActive() ? 'bg-primary text-primary-foreground' : 'hover:bg-accent text-foreground',
                ]"
                @click="action.command()"
            >
                {{ action.icon }}
            </button>
        </div>

        <!-- Editor area -->
        <EditorContent :editor="editor" />
    </div>
</template>
