<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    ChevronDown,
    Eye,
    Plus,
    Star,
    Trash2,
    Upload,
    X,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';

const fieldClass =
    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';
const textareaClass = `${fieldClass} min-h-24`;

interface GalleryItem {
    id: number;
    name: string;
    file_name: string;
    url: string;
    size: number;
    is_cover: boolean;
    order_column: number | null;
}

interface PersonTag {
    name: string;
    role: string;
    url: string;
    visible: boolean;
}

interface MomentProps {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    cover_media_id: number | null;
    status: 'draft' | 'published';
    published_at: string | null;
    featured: boolean;
    sort_order: number;
    external_link: string | null;
    external_link_label: string | null;
    people: PersonTag[];
    seo: {
        title: string;
        description: string;
        canonical_url: string;
    };
}

const props = defineProps<{
    moment: MomentProps;
    gallery: GalleryItem[];
}>();

const form = useForm({
    _method: 'put',
    title: props.moment.title,
    slug: props.moment.slug,
    description: props.moment.description ?? '',
    cover_media_id: props.moment.cover_media_id,
    status: props.moment.status,
    published_at: props.moment.published_at ?? '',
    featured: Boolean(props.moment.featured),
    sort_order: props.moment.sort_order,
    external_link: props.moment.external_link ?? '',
    external_link_label: props.moment.external_link_label ?? '',
    people: Array.isArray(props.moment.people) ? [...props.moment.people] : [],
    images: [] as File[],
    seo: {
        title: props.moment.seo.title,
        description: props.moment.seo.description,
        canonical_url: props.moment.seo.canonical_url,
    },
});

const localGallery = ref<GalleryItem[]>([...props.gallery]);

// Re-sync after saves/uploads, which return fresh props from the server.
watch(
    () => props.gallery,
    (fresh) => {
        localGallery.value = [...fresh];
    },
);
watch(
    () => props.moment.cover_media_id,
    (coverId) => {
        form.cover_media_id = coverId;
    },
);

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
const newImagePreviews = ref<{ file: File; url: string }[]>([]);
const isReordering = ref(false);

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
        newImagePreviews.value.push({
            file,
            url: URL.createObjectURL(file),
        });
    }

    input.value = '';
}

function removeNewImage(index: number): void {
    const preview = newImagePreviews.value[index];

    if (preview) {
        URL.revokeObjectURL(preview.url);
    }

    newImagePreviews.value.splice(index, 1);
    form.images.splice(index, 1);
}

function setCover(item: GalleryItem): void {
    form.cover_media_id = item.id;
    localGallery.value.forEach((m) => {
        m.is_cover = m.id === item.id;
    });
}

function moveImage(fromIdx: number, toIdx: number): void {
    if (toIdx < 0 || toIdx >= localGallery.value.length) {
        return;
    }

    const item = localGallery.value.splice(fromIdx, 1)[0];
    localGallery.value.splice(toIdx, 0, item);

    isReordering.value = true;
    router.post(
        `/dashboard/galleries/${props.moment.id}/media/reorder`,
        {
            order: localGallery.value.map((m) => m.id),
        },
        {
            preserveScroll: true,
            onFinish: () => {
                isReordering.value = false;
            },
        },
    );
}

function deleteMedia(item: GalleryItem): void {
    if (!confirm('Remove this photo from the gallery?')) {
        return;
    }

    router.delete(`/dashboard/galleries/${props.moment.id}/media/${item.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            const idx = localGallery.value.findIndex((m) => m.id === item.id);

            if (idx !== -1) {
                localGallery.value.splice(idx, 1);
            }

            if (form.cover_media_id === item.id) {
                form.cover_media_id = localGallery.value[0]?.id ?? null;
            }
        },
    });
}

function handleSubmit(): void {
    form.post(`/dashboard/galleries/${props.moment.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            newImagePreviews.value = [];
            form.images = [];
        },
    });
}

function handleCancel(): void {
    router.visit('/dashboard/galleries');
}
</script>

<template>
    <Head :title="`Edit ${moment.title}`" />

    <FormPage
        :title="`Edit ${moment.title}`"
        description="Update gallery photos, set cover photo, and manage details."
    >
        <template #actions>
            <Button
                v-if="moment.status === 'published'"
                as-child
                variant="outline"
                size="sm"
            >
                <a
                    :href="`/moments/${moment.slug}`"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <Eye class="mr-1.5 h-3.5 w-3.5" />
                    View Public Page
                </a>
            </Button>
        </template>

        <form @submit.prevent="handleSubmit" class="space-y-8">
            <!-- 1. Basic Details -->
            <FormSection
                title="Basic Information"
                description="Title and description for this gallery."
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
                            placeholder="Brief description of the match or training session..."
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

            <!-- 2. Gallery Photos & Cover Selection -->
            <FormSection
                title="Gallery Photos"
                description="Upload images, choose the cover photo, and adjust display order."
            >
                <div class="space-y-5">
                    <!-- Current Gallery Grid -->
                    <div v-if="localGallery.length > 0">
                        <div
                            class="text-muted-foreground mb-3 flex items-center justify-between text-xs font-semibold uppercase tracking-wider"
                        >
                            <span>
                                {{ localGallery.length }}
                                {{
                                    localGallery.length === 1
                                        ? 'Photo'
                                        : 'Photos'
                                }}
                                in Gallery
                            </span>
                            <span
                                v-if="isReordering"
                                class="text-primary animate-pulse text-xs"
                            >
                                Saving order...
                            </span>
                        </div>

                        <div
                            class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
                        >
                            <div
                                v-for="(item, idx) in localGallery"
                                :key="item.id"
                                class="border-border bg-card shadow-xs group relative flex flex-col overflow-hidden rounded-lg border transition-[box-shadow,border-color]"
                                :class="
                                    item.is_cover ||
                                    form.cover_media_id === item.id
                                        ? 'ring-primary border-primary ring-2'
                                        : ''
                                "
                            >
                                <div
                                    class="aspect-4/3 bg-muted relative overflow-hidden"
                                >
                                    <img
                                        :src="item.url"
                                        :alt="item.name"
                                        class="h-full w-full object-cover"
                                    />

                                    <!-- Cover Badge or Make Cover Action -->
                                    <button
                                        v-if="
                                            item.is_cover ||
                                            form.cover_media_id === item.id
                                        "
                                        type="button"
                                        class="bg-primary text-primary-foreground absolute left-2 top-2 flex items-center gap-1 rounded px-2 py-0.5 text-[10px] font-bold shadow-sm"
                                        title="Current Cover"
                                    >
                                        <Star class="h-3 w-3 fill-current" />
                                        COVER
                                    </button>
                                    <button
                                        v-else
                                        type="button"
                                        @click="setCover(item)"
                                        class="hover:bg-primary absolute left-2 top-2 flex items-center gap-1 rounded bg-black/70 px-2 py-0.5 text-[10px] font-medium text-white opacity-0 shadow-sm transition-opacity group-hover:opacity-100"
                                        title="Set as Cover Photo"
                                    >
                                        <Star class="h-3 w-3" />
                                        Make Cover
                                    </button>

                                    <!-- Delete Button -->
                                    <button
                                        type="button"
                                        @click="deleteMedia(item)"
                                        class="hover:bg-destructive absolute right-2 top-2 rounded-full bg-black/70 p-1.5 text-white opacity-0 transition-opacity group-hover:opacity-100"
                                        title="Delete photo"
                                    >
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </button>
                                </div>

                                <!-- Card Footer: Quick Reorder -->
                                <div
                                    class="border-border bg-muted/30 flex items-center justify-between border-t px-2.5 py-1.5 text-xs"
                                >
                                    <span
                                        class="text-muted-foreground font-mono text-[10px]"
                                    >
                                        #{{ idx + 1 }}
                                    </span>
                                    <div class="flex items-center gap-1">
                                        <button
                                            type="button"
                                            :disabled="idx === 0"
                                            @click="moveImage(idx, idx - 1)"
                                            class="text-muted-foreground hover:bg-muted hover:text-foreground cursor-pointer rounded p-1 disabled:opacity-20"
                                            title="Move earlier"
                                        >
                                            <ArrowLeft class="h-3.5 w-3.5" />
                                        </button>
                                        <button
                                            type="button"
                                            :disabled="
                                                idx === localGallery.length - 1
                                            "
                                            @click="moveImage(idx, idx + 1)"
                                            class="text-muted-foreground hover:bg-muted hover:text-foreground cursor-pointer rounded p-1 disabled:opacity-20"
                                            title="Move later"
                                        >
                                            <ArrowRight class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload More Photos Dropzone -->
                    <div>
                        <label
                            class="border-border hover:border-primary hover:bg-muted/30 flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed p-6 text-center transition-colors"
                        >
                            <Upload
                                class="text-muted-foreground mb-1.5 h-6 w-6"
                            />
                            <span class="text-foreground text-sm font-medium"
                                >Click to select photos or drag & drop</span
                            >
                            <span class="text-muted-foreground mt-0.5 text-xs"
                                >JPG, PNG, or WebP (up to 15MB each)</span
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

                        <!-- New image staged previews -->
                        <div v-if="newImagePreviews.length > 0" class="mt-4">
                            <div
                                class="text-primary mb-2 text-xs font-semibold"
                            >
                                Staged for upload ({{ newImagePreviews.length }}
                                new
                                {{
                                    newImagePreviews.length === 1
                                        ? 'photo'
                                        : 'photos'
                                }}
                                - will upload when saving):
                            </div>
                            <div
                                class="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6"
                            >
                                <div
                                    v-for="(preview, idx) in newImagePreviews"
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
                                        @click="removeNewImage(idx)"
                                        class="hover:bg-destructive absolute right-1 top-1 rounded-full bg-black/70 p-1 text-white opacity-0 transition-opacity group-hover:opacity-100"
                                        title="Remove"
                                    >
                                        <X class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </FormSection>

            <!-- 3. Publishing & Visibility -->
            <FormSection
                title="Publishing"
                description="Set visibility and featured placement."
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
                            placeholder="auto-generated-slug"
                        />
                        <p class="text-muted-foreground mt-1 text-xs">
                            Public URL: /moments/{{ form.slug }}
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
                                placeholder="SEO Title (defaults to gallery title)"
                            />
                        </div>
                        <div>
                            <textarea
                                v-model="form.seo.description"
                                :class="textareaClass"
                                rows="2"
                                placeholder="SEO Description (defaults to gallery description)"
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
                    {{ form.processing ? 'Saving...' : 'Save Gallery' }}
                </Button>
            </PageActions>
        </form>
    </FormPage>
</template>
