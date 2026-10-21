<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { Plus, Trash2, ArrowUp, ArrowDown, GripVertical } from '@lucide/vue';
import { ref } from 'vue';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import { index as menuIndex, store as menuStore } from '@/routes/menus';

interface MenuItemInput {
    label: string;
    url: string;
    target: string;
    sort_order: number;
}

const fieldClass =
    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';

const form = useForm({
    name: '',
    slug: '',
    location: '',
    items: [] as MenuItemInput[],
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
    if (index === 0) {
        return;
    }

    const item = form.items[index];
    form.items.splice(index, 1);
    form.items.splice(index - 1, 0, item);
    reorder();
}

function moveDown(index: number): void {
    if (index === form.items.length - 1) {
        return;
    }

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

function generateSlug(): void {
    if (!form.name) {
        return;
    }

    form.slug = form.name
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}

function handleSubmit(): void {
    form.submit(menuStore());
}

function handleCancel(): void {
    router.visit(menuIndex());
}
</script>

<template>
    <FormPage title="Create Menu" description="Add a new navigation menu.">
        <form @submit.prevent="handleSubmit" class="space-y-12">
            <FormSection
                title="Basic Information"
                description="General name, slug identifier, and placement region."
            >
                <div class="space-y-6">
                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Name <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            :class="fieldClass"
                            placeholder="e.g. Main Navigation"
                            @blur="generateSlug"
                        />
                        <p
                            v-if="form.errors.name"
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
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
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.slug }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Location <span class="text-destructive">*</span>
                        </label>
                        <select v-model="form.location" :class="fieldClass">
                            <option value="">Select a location</option>
                            <option value="main">
                                Main Navigation (Header)
                            </option>
                            <option value="footer">Footer Links</option>
                            <option value="mobile">Mobile Only</option>
                        </select>
                        <p
                            v-if="form.errors.location"
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.location }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <!-- Menu Items Builder -->
            <FormSection
                title="Menu Items"
                description="Add and reorder links for this menu. Drag items to rearrange order."
            >
                <div class="space-y-4">
                    <div
                        v-if="form.items.length === 0"
                        class="border-border text-muted-foreground rounded-lg border border-dashed p-6 text-center text-sm"
                    >
                        No menu items added yet. Click "Add Item" to start
                        building your navigation.
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
                            draggedIndex === idx
                                ? 'border-purple-500 bg-purple-50/50 opacity-40'
                                : 'bg-card border-border',
                            'shadow-xs flex cursor-move flex-col items-stretch gap-3 rounded-lg border p-4 transition-all duration-150 sm:flex-row sm:items-center',
                        ]"
                    >
                        <div class="flex shrink-0 items-center gap-2">
                            <div
                                class="text-muted-foreground hover:text-foreground hover:bg-muted cursor-grab rounded-md p-1 active:cursor-grabbing"
                                title="Drag to reorder"
                            >
                                <GripVertical class="h-5 w-5" />
                            </div>
                            <div class="flex items-center gap-0.5">
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    :disabled="idx === 0"
                                    @click="moveUp(idx)"
                                    title="Move Up"
                                >
                                    <ArrowUp class="h-3.5 w-3.5" />
                                </Button>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    :disabled="idx === form.items.length - 1"
                                    @click="moveDown(idx)"
                                    title="Move Down"
                                >
                                    <ArrowDown class="h-3.5 w-3.5" />
                                </Button>
                            </div>
                            <span
                                class="text-muted-foreground w-6 text-center font-mono text-xs"
                                >#{{ idx + 1 }}</span
                            >
                        </div>

                        <div
                            class="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-2"
                        >
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
                            <Trash2 class="h-4 w-4" />
                        </Button>
                    </div>

                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        class="flex items-center gap-2"
                        @click="addItem"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Add Menu Item</span>
                    </Button>
                </div>
            </FormSection>

            <PageActions>
                <Button type="button" variant="outline" @click="handleCancel">
                    Cancel
                </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creating...' : 'Create Menu' }}
                </Button>
            </PageActions>
        </form>
    </FormPage>
</template>
