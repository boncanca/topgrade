<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ChevronDown, Plus, Trash2, Upload, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';

const fieldClass =
    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';
const textareaClass = `${fieldClass} min-h-24`;

interface PersonTag {
    name: string;
    role: string;
    url: string;
    visible: boolean;
}

const form = useForm({
    title: '',
    slug: '',
    description: '',
    status: 'published' as 'draft' | 'published',
    published_at: new Date().toISOString().slice(0, 16),
    featured: false,
    sort_order: 0,
    external_link: '',
    external_link_label: '',
    people: [] as PersonTag[],
    images: [] as File[],
    seo: {
        title: '',
        description: '',
        canonical_url: '',
    },
});

const MAX_IMAGE_BYTES = 15 * 1024 * 1024;
const MAX_BATCH_BYTES = 50 * 1024 * 1024;
const MAX_BATCH_COUNT = 15;

const clientErrors = ref<string[]>([]);
const imageErrors = computed(() => [
    ...clientErrors.value,
    ...Object.entries(form.errors)
        .filter(([key]) => key === 'images' || key.startsWith('images.'))
        .map(([, message]) => message),
]);

const imagePreviews = ref<{ file: File; url: string }[]>([]);

function generateSlug(): void {
    if (!form.title) {
        return;
    }

    form.slug = form.title
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}

function addPerson(): void {
    form.people.push({
        name: '',
        role: '',
        url: '',
        visible: false,
    });
}

function removePerson(index: number): void {
    form.people.splice(index, 1);
}

function onFileSelect(e: Event): void {
    const input = e.target as HTMLInputElement;

    if (!input.files || input.files.length === 0) {
        return;
    }

    clientErrors.value = [];

    if (form.images.length + input.files.length > MAX_BATCH_COUNT) {
        clientErrors.value.push(
            `Cannot upload more than ${MAX_BATCH_COUNT} images in one batch.`,
        );
        input.value = '';

        return;
    }

    let batchBytes = form.images.reduce((sum, f) => sum + f.size, 0);

    for (let i = 0; i < input.files.length; i++) {
        const file = input.files[i];

        if (!file.type.startsWith('image/')) {
            clientErrors.value.push(
                `${file.name} is not an image and was skipped.`,
            );
            continue;
        }

        if (file.size > MAX_IMAGE_BYTES) {
            clientErrors.value.push(
                `${file.name} is larger than 15MB and was skipped.`,
            );
            continue;
        }

        if (batchBytes + file.size > MAX_BATCH_BYTES) {
            clientErrors.value.push(
                `Total batch size exceeds 50MB. ${file.name} and subsequent files were skipped.`,
            );
            break;
        }

        batchBytes += file.size;
        form.images.push(file);
        imagePreviews.value.push({
            file,
            url: URL.createObjectURL(file),
        });
    }

    input.value = '';
}

function removeImage(index: number): void {
    const preview = imagePreviews.value[index];

    if (preview) {
        URL.revokeObjectURL(preview.url);
    }

    imagePreviews.value.splice(index, 1);
    form.images.splice(index, 1);
}

function handleSubmit(): void {
    form.post('/dashboard/galleries');
}

function handleCancel(): void {
    router.visit('/dashboard/galleries');
}
</script>

<template>
    <Head title="Create New Gallery" />

    <FormPage
        title="Create New Gallery"
        description="Add a new photo gallery, matchday visual set, or training showcase."
    >
        <form @submit.prevent="handleSubmit" class="space-y-8">
            <!-- 1. Basic Details -->
            <FormSection
                title="Basic Information"
                description="Title and summary for this gallery."
            >
                <div class="space-y-4">
                    <div>
                        <label
                            class="text-foreground mb-1.5 block text-sm font-medium"
                        >
                            Title <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            :class="fieldClass"
                            placeholder="e.g. Saturday League Match vs Hackney"
                            required
                            @blur="generateSlug"
                        />
                        <p
                            v-if="form.errors.title"
                            class="text-destructive mt-1 text-xs"
                        >
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="text-foreground mb-1.5 block text-sm font-medium"
                        >
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            :class="textareaClass"
                            rows="2"
                            placeholder="Brief context about this matchday or training session..."
                        />
                        <p
                            v-if="form.errors.description"
                            class="text-destructive mt-1 text-xs"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <!-- 2. Gallery Photos Upload -->
            <FormSection
                title="Gallery Photos"
                description="Upload images for this gallery (cover photo can be selected after upload)."
            >
                <div class="space-y-4">
                    <label
                        class="border-border hover:border-primary hover:bg-muted/30 flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed p-6 text-center transition-colors"
                    >
                        <Upload class="text-muted-foreground mb-1.5 h-6 w-6" />
                        <span class="text-foreground text-sm font-medium"
                            >Click to select photos or drag & drop</span
                        >
                        <span class="text-muted-foreground mt-0.5 text-xs"
                            >Select multiple JPG, PNG, or WebP images (up to
                            15MB each)</span
                        >
                        <input
                            type="file"
                            multiple
                            accept="image/*"
                            class="hidden"
                            @change="onFileSelect"
                        />
                    </label>

                    <ul
                        v-if="imageErrors.length > 0"
                        class="text-destructive mt-3 space-y-1 text-sm"
                        role="alert"
                    >
                        <li v-for="(message, i) in imageErrors" :key="i">
                            {{ message }}
                        </li>
                    </ul>

                    <div v-if="form.processing && form.progress" class="mt-3">
                        <div
                            class="bg-muted h-2 w-full overflow-hidden rounded"
                        >
                            <div
                                class="bg-primary h-full transition-all"
                                :style="{
                                    width: `${form.progress.percentage}%`,
                                }"
                            />
                        </div>
                        <p class="text-muted-foreground mt-1 text-xs">
                            Uploading… {{ form.progress.percentage }}%
                        </p>
                    </div>

                    <!-- Selected Photos Previews -->
                    <div v-if="imagePreviews.length > 0" class="mt-4">
                        <div class="text-primary mb-2 text-xs font-semibold">
                            {{ imagePreviews.length }}
                            {{
                                imagePreviews.length === 1 ? 'Photo' : 'Photos'
                            }}
                            selected for upload:
                        </div>
                        <div
                            class="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6"
                        >
                            <div
                                v-for="(preview, idx) in imagePreviews"
                                :key="idx"
                                class="border-border bg-muted group relative aspect-square overflow-hidden rounded-md border"
                            >
                                <img
                                    :src="preview.url"
                                    :alt="preview.file.name"
                                    class="h-full w-full object-cover"
                                />
                                <button
                                    type="button"
                                    @click="removeImage(idx)"
                                    class="hover:bg-destructive absolute right-1 top-1 rounded-full bg-black/70 p-1 text-white opacity-0 transition-opacity group-hover:opacity-100"
                                    title="Remove"
                                >
                                    <X class="h-3.5 w-3.5" />
                                </button>
                                <div
                                    class="absolute inset-x-0 bottom-0 truncate bg-black/60 px-1 py-0.5 text-[10px] text-white"
                                >
                                    {{ preview.file.name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </FormSection>

            <!-- 3. Publishing & Visibility -->
            <FormSection
                title="Publishing"
                description="Control visibility and featured status."
            >
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label
                            class="text-foreground mb-1.5 block text-sm font-medium"
                        >
                            Status <span class="text-destructive">*</span>
                        </label>
                        <select v-model="form.status" :class="fieldClass">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="text-foreground mb-1.5 block text-sm font-medium"
                        >
                            Publish Date
                        </label>
                        <input
                            v-model="form.published_at"
                            type="datetime-local"
                            :class="fieldClass"
                        />
                    </div>

                    <div class="flex items-end pb-1">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input
                                v-model="form.featured"
                                type="checkbox"
                                class="border-input text-primary focus:ring-ring h-4 w-4 rounded"
                            />
                            <span class="text-foreground text-sm font-medium">
                                Feature on Homepage
                            </span>
                        </label>
                    </div>
                </div>
            </FormSection>

            <!-- 4. Advanced Options (Collapsible) -->
            <details
                class="border-border bg-card group rounded-lg border p-4 transition-colors"
            >
                <summary
                    class="text-foreground flex cursor-pointer select-none items-center justify-between text-sm font-medium"
                >
                    <span
                        >Advanced Options (Slug, External Link, Tagging,
                        SEO)</span
                    >
                    <ChevronDown
                        class="text-muted-foreground h-4 w-4 transition-transform group-open:rotate-180"
                    />
                </summary>

                <div class="border-border mt-6 space-y-6 border-t pt-6">
                    <!-- URL Slug -->
                    <div>
                        <label
                            class="text-foreground mb-1.5 block text-xs font-semibold uppercase tracking-wider"
                        >
                            URL Slug
                        </label>
                        <input
                            v-model="form.slug"
                            type="text"
                            :class="fieldClass"
                            placeholder="auto-generated-from-title"
                        />
                        <p class="text-muted-foreground mt-1 text-xs">
                            Public URL: /moments/{{ form.slug || '...' }}
                        </p>
                    </div>

                    <!-- External Link -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label
                                class="text-foreground mb-1.5 block text-xs font-semibold uppercase tracking-wider"
                            >
                                External Link URL (Optional)
                            </label>
                            <input
                                v-model="form.external_link"
                                type="url"
                                :class="fieldClass"
                                placeholder="https://..."
                            />
                        </div>
                        <div>
                            <label
                                class="text-foreground mb-1.5 block text-xs font-semibold uppercase tracking-wider"
                            >
                                Link Label
                            </label>
                            <input
                                v-model="form.external_link_label"
                                type="text"
                                :class="fieldClass"
                                placeholder="e.g. Match Report"
                            />
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="space-y-3">
                        <label
                            class="text-foreground block text-xs font-semibold uppercase tracking-wider"
                        >
                            Search Engine Optimization
                        </label>
                        <div>
                            <input
                                v-model="form.seo.title"
                                type="text"
                                :class="fieldClass"
                                placeholder="SEO Title (defaults to title)"
                            />
                        </div>
                        <div>
                            <textarea
                                v-model="form.seo.description"
                                :class="textareaClass"
                                rows="2"
                                placeholder="SEO Description (defaults to description)"
                            />
                        </div>
                    </div>

                    <!-- People & Staff Tagging -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label
                                class="text-foreground block text-xs font-semibold uppercase tracking-wider"
                            >
                                Tag People or Staff (Optional)
                            </label>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="addPerson"
                            >
                                <Plus class="mr-1 h-3.5 w-3.5" />
                                Add Person
                            </Button>
                        </div>

                        <div v-if="form.people.length > 0" class="space-y-3">
                            <div
                                v-for="(person, idx) in form.people"
                                :key="idx"
                                class="border-border bg-muted/40 space-y-2 rounded-md border p-3"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-muted-foreground text-xs font-medium"
                                        >Person #{{ idx + 1 }}</span
                                    >
                                    <button
                                        type="button"
                                        @click="removePerson(idx)"
                                        class="text-destructive flex cursor-pointer items-center gap-1 text-xs hover:underline"
                                    >
                                        <Trash2 class="h-3 w-3" />
                                        Remove
                                    </button>
                                </div>
                                <div
                                    class="grid grid-cols-1 gap-2 sm:grid-cols-3"
                                >
                                    <input
                                        v-model="person.name"
                                        type="text"
                                        :class="fieldClass"
                                        placeholder="Name (e.g. Coach Michael)"
                                    />
                                    <input
                                        v-model="person.role"
                                        type="text"
                                        :class="fieldClass"
                                        placeholder="Role (e.g. Head Coach)"
                                    />
                                    <input
                                        v-model="person.url"
                                        type="url"
                                        :class="fieldClass"
                                        placeholder="Link (optional)"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </details>

            <!-- Page Actions -->
            <PageActions>
                <Button type="button" variant="outline" @click="handleCancel">
                    Cancel
                </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creating...' : 'Create Gallery' }}
                </Button>
            </PageActions>
        </form>
    </FormPage>
</template>
