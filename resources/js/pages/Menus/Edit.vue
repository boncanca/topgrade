<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import { index as menuIndex, update as menuUpdate } from '@/routes/menus';
import { Plus, Trash2, ArrowUp, ArrowDown, GripVertical } from '@lucide/vue';

interface MenuItem {
    id?: number;
    label: string;
    url: string | null;
    target?: string | null;
    sort_order?: number;
}

interface Menu {
    id: number;
    name: string;
    slug: string;
    location: string;
    items?: MenuItem[];
}

const props = defineProps<{
    menu: Menu;
}>();

const fieldClass =
    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';

const form = useForm({
    name: props.menu.name,
    slug: props.menu.slug,
    location: props.menu.location,
    items: (props.menu.items ?? []).map((item, idx) => ({
        label: item.label,
        url: item.url ?? '/',
        target: item.target ?? '_self',
        sort_order: item.sort_order ?? idx + 1,
    })),
});

const draggedIndex = ref<number | null>(null);

function onDragStart(index: number, event: DragEvent): void {
    draggedIndex.value = index;
    if (event.dataTransfer) {
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', String(index));
    }
}

function onDragOver(index: number, event: DragEvent): void {
    event.preventDefault();
    if (event.dataTransfer) {
        event.dataTransfer.dropEffect = 'move';
    }
}

function onDrop(targetIndex: number, event: DragEvent): void {
    event.preventDefault();
    if (draggedIndex.value !== null && draggedIndex.value !== targetIndex) {
        const item = form.items[draggedIndex.value];
        form.items.splice(draggedIndex.value, 1);
        form.items.splice(targetIndex, 0, item);
        reorder();
    }
    draggedIndex.value = null;
}

function addItem(): void {
    form.items.push({
        label: '',
        url: '/',
        target: '_self',
        sort_order: form.items.length + 1,
    });
}

function removeItem(index: number): void {
    form.items.splice(index, 1);
    reorder();
}

function moveUp(index: number): void {
    if (index === 0) return;
    const item = form.items[index];
    form.items.splice(index, 1);
    form.items.splice(index - 1, 0, item);
    reorder();
}

function moveDown(index: number): void {
    if (index === form.items.length - 1) return;
    const item = form.items[index];
    form.items.splice(index, 1);
    form.items.splice(index + 1, 0, item);
    reorder();
}

function reorder(): void {
    form.items.forEach((item, idx) => {
        item.sort_order = idx + 1;
    });
}

function handleSubmit(): void {
    form.submit(menuUpdate(props.menu.id));
}

function handleCancel(): void {
    router.visit(menuIndex());
}
</script>

<template>
    <FormPage
        title="Edit Menu"
        description="Update menu configuration and links."
    >
        <form @submit.prevent="handleSubmit" class="space-y-12">
            <FormSection
                title="Basic Information"
                description="General name, slug identifier, and placement region."
            >
                <div class="space-y-6">
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
                            Name <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            :class="fieldClass"
                            placeholder="e.g. Main Navigation"
                        />
                        <p
                            v-if="form.errors.name"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
                            Slug <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.slug"
                            type="text"
                            :class="fieldClass"
                            placeholder="e.g. main-navigation"
                        />
                        <p
                            v-if="form.errors.slug"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.slug }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
                            Location <span class="text-destructive">*</span>
                        </label>
                        <select v-model="form.location" :class="fieldClass">
                            <option value="">Select a location</option>
                            <option value="main">Main Navigation (Header)</option>
                            <option value="footer">Footer Links</option>
                            <option value="mobile">Mobile Only</option>
                        </select>
                        <p
                            v-if="form.errors.location"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.location }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <!-- Menu Items Builder -->
            <FormSection
                title="Menu Items"
                description="Add, edit, or reorder links in this menu. Drag items to rearrange order."
            >
                <div class="space-y-4">
                    <div v-if="form.items.length === 0" class="rounded-lg border border-dashed border-border p-6 text-center text-sm text-muted-foreground">
                        No menu items in this menu. Click "Add Item" to add navigation links.
                    </div>

                    <div
                        v-for="(item, idx) in form.items"
                        :key="idx"
                        draggable="true"
                        @dragstart="onDragStart(idx, $event)"
                        @dragover="onDragOver(idx, $event)"
                        @drop="onDrop(idx, $event)"
                        @dragend="draggedIndex = null"
                        :class="[
                            draggedIndex === idx ? 'opacity-40 border-purple-500 bg-purple-50/50' : 'bg-card border-border',
                            'flex flex-col sm:flex-row items-stretch sm:items-center gap-3 p-4 rounded-lg border shadow-xs transition-all duration-150 cursor-move'
                        ]"
                    >
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="cursor-grab active:cursor-grabbing text-muted-foreground hover:text-foreground p-1 rounded-md hover:bg-muted" title="Drag to reorder">
                                <GripVertical class="w-5 h-5" />
                            </div>
                            <div class="flex items-center gap-0.5">
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="xs"
                                    :disabled="idx === 0"
                                    @click="moveUp(idx)"
                                    title="Move Up"
                                >
                                    <ArrowUp class="w-3.5 h-3.5" />
                                </Button>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="xs"
                                    :disabled="idx === form.items.length - 1"
                                    @click="moveDown(idx)"
                                    title="Move Down"
                                >
                                    <ArrowDown class="w-3.5 h-3.5" />
                                </Button>
                            </div>
                            <span class="text-xs font-mono text-muted-foreground w-6 text-center">#{{ idx + 1 }}</span>
                        </div>

                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input
                                v-model="item.label"
                                type="text"
                                :class="fieldClass"
                                placeholder="Label (e.g. Training)"
                            />
                            <input
                                v-model="item.url"
                                type="text"
                                :class="fieldClass"
                                placeholder="URL (e.g. /training)"
                            />
                        </div>

                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            class="text-destructive hover:text-destructive shrink-0"
                            @click="removeItem(idx)"
                        >
                            <Trash2 class="w-4 h-4" />
                        </Button>
                    </div>

                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        class="flex items-center gap-2"
                        @click="addItem"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Add Menu Item</span>
                    </Button>
                </div>
            </FormSection>

            <PageActions>
                <Button type="button" variant="outline" @click="handleCancel">
                    Cancel
                </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </Button>
            </PageActions>
        </form>
    </FormPage>
</template>
