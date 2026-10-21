<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
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

    // Send new order to server
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
        description="Update gallery details, select cover photo, reorder images, or add new photos."
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

        <form @submit.prevent="handleSubmit" class="space-y-10">
            <!-- Gallery Images Management -->
            <FormSection
                title="Gallery Media & Cover Photo"
                description="Manage existing photos, set the cover image, and arrange the presentation order."
            >
                <div class="space-y-6">
                    <!-- Existing Gallery Grid -->
                    <div v-if="localGallery.length > 0">
                        <div
                            class="text-muted-foreground mb-3 flex items-center justify-between text-xs font-semibold uppercase tracking-wider"
                        >
                            <span>
                                Current Gallery ({{ localGallery.length }}
                                {{
                                    localGallery.length === 1
                                        ? 'photo'
                                        : 'photos'
                                }})
                            </span>
                            <span
                                v-if="isReordering"
                                class="text-primary animate-pulse text-xs"
                            >
                                Updating order...
                            </span>
                        </div>

                        <div
                            class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
                        >
                            <div
                                v-for="(item, idx) in localGallery"
                                :key="item.id"
                                class="border-border bg-card shadow-xs group relative flex flex-col overflow-hidden rounded-lg border"
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

                                    <!-- Cover Badge -->
                                    <div
                                        v-if="
                                            item.is_cover ||
                                            form.cover_media_id === item.id
                                        "
                                        class="bg-primary text-primary-foreground absolute left-2 top-2 flex items-center gap-1 rounded px-2 py-0.5 text-[10px] font-bold shadow-sm"
                                    >
                                        <Star class="h-3 w-3 fill-current" />
                                        COVER
                                    </div>

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

                                <!-- Card Footer: Reorder & Set Cover -->
                                <div
                                    class="border-border bg-muted/30 flex items-center justify-between gap-1 border-t p-2.5 text-xs"
                                >
                                    <!-- Cover Button -->
                                    <button
                                        type="button"
                                        @click="setCover(item)"
                                        class="rounded px-2 py-1 text-[11px] font-medium transition-colors"
                                        :class="
                                            item.is_cover ||
                                            form.cover_media_id === item.id
                                                ? 'bg-primary/10 text-primary font-bold'
                                                : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                                        "
                                    >
                                        {{
                                            item.is_cover ||
                                            form.cover_media_id === item.id
                                                ? 'Cover'
                                                : 'Make Cover'
                                        }}
                                    </button>

                                    <!-- Reorder Controls -->
                                    <div class="flex items-center gap-1">
                                        <button
                                            type="button"
                                            :disabled="idx === 0"
                                            @click="moveImage(idx, idx - 1)"
                                            class="text-muted-foreground hover:bg-muted hover:text-foreground rounded p-1 disabled:opacity-20"
                                            title="Move left"
                                        >
                                            <ArrowLeft class="h-3.5 w-3.5" />
                                        </button>
                                        <span
                                            class="text-muted-foreground px-1 font-mono text-[10px]"
                                        >
                                            #{{ idx + 1 }}
                                        </span>
                                        <button
                                            type="button"
                                            :disabled="
                                                idx === localGallery.length - 1
                                            "
                                            @click="moveImage(idx, idx + 1)"
                                            class="text-muted-foreground hover:bg-muted hover:text-foreground rounded p-1 disabled:opacity-20"
                                            title="Move right"
                                        >
                                            <ArrowRight class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="border-border text-muted-foreground rounded-lg border border-dashed p-6 text-center text-sm"
                    >
                        No photos currently in this gallery. Upload photos
                        below.
                    </div>

                    <!-- Upload More Photos -->
                    <div class="border-border border-t pt-4">
                        <label
                            class="text-muted-foreground mb-2 block text-xs font-semibold uppercase tracking-wider"
                        >
                            Add More Photos
                        </label>
                        <label
                            class="border-border hover:border-primary/50 hover:bg-muted/30 flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed p-6 text-center transition-colors"
                        >
                            <Upload
                                class="text-muted-foreground mb-1 h-6 w-6"
                            />
                            <span class="text-foreground text-sm font-medium"
                                >Click to select photos to append</span
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
                        <div
                            v-if="form.processing && form.progress"
                            class="mt-3"
                        >
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

                        <!-- Previews of newly selected photos -->
                        <div v-if="newImagePreviews.length > 0" class="mt-4">
                            <div
                                class="text-primary mb-2 text-xs font-semibold"
                            >
                                Ready to upload:
                                {{ newImagePreviews.length }} new
                                {{
                                    newImagePreviews.length === 1
                                        ? 'photo'
                                        : 'photos'
                                }}
                                (click Save Changes to upload)
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
                                    <div
                                        class="absolute inset-x-0 bottom-0 truncate bg-black/60 px-1 py-0.5 text-[10px] text-white"
                                    >
                                        {{ preview.file.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </FormSection>

            <!-- Basic Details -->
            <FormSection
                title="Basic Information"
                description="Title, slug, and description."
            >
                <div class="space-y-6">
                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Title <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            :class="fieldClass"
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
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Slug
                        </label>
                        <input
                            v-model="form.slug"
                            type="text"
                            :class="fieldClass"
                        />
                        <p class="text-muted-foreground mt-1 text-xs">
                            Public URL identifier (/moments/{{ form.slug }})
                        </p>
                        <p
                            v-if="form.errors.slug"
                            class="text-destructive mt-1 text-xs"
                        >
                            {{ form.errors.slug }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            :class="textareaClass"
                            rows="3"
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

            <!-- Publishing & Status -->
            <FormSection
                title="Publishing Status"
                description="Control visibility and featured status."
            >
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Status <span class="text-destructive">*</span>
                        </label>
                        <select v-model="form.status" :class="fieldClass">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                        <p
                            v-if="form.errors.status"
                            class="text-destructive mt-1 text-xs"
                        >
                            {{ form.errors.status }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Published Date & Time
                        </label>
                        <input
                            v-model="form.published_at"
                            type="datetime-local"
                            :class="fieldClass"
                        />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input
                                v-model="form.featured"
                                type="checkbox"
                                class="border-input text-primary focus:ring-ring h-4 w-4 rounded"
                            />
                            <span class="text-foreground text-sm font-medium">
                                Feature this Moment on the Public Moments Hero
                                Carousel
                            </span>
                        </label>
                    </div>
                </div>
            </FormSection>

            <!-- People / Tags -->
            <FormSection
                title="People & Staff Tagging"
                description="Associate coaches, team members, or staff. Control whether their names are displayed publicly."
            >
                <div class="space-y-4">
                    <div
                        v-for="(person, idx) in form.people"
                        :key="idx"
                        class="border-border bg-card space-y-3 rounded-md border p-4"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="text-muted-foreground text-xs font-semibold uppercase tracking-wider"
                            >
                                Person #{{ idx + 1 }}
                            </span>
                            <button
                                type="button"
                                @click="removePerson(idx)"
                                class="text-destructive flex items-center gap-1 text-xs hover:underline"
                            >
                                <Trash2 class="h-3 w-3" />
                                Remove
                            </button>
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div>
                                <label
                                    class="text-foreground mb-1 block text-xs font-medium"
                                    >Name</label
                                >
                                <input
                                    v-model="person.name"
                                    type="text"
                                    :class="fieldClass"
                                    placeholder="e.g. Coach Michael"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="text-foreground mb-1 block text-xs font-medium"
                                    >Role / Context</label
                                >
                                <input
                                    v-model="person.role"
                                    type="text"
                                    :class="fieldClass"
                                    placeholder="e.g. Head Coach / U12 Squad"
                                />
                            </div>
                            <div>
                                <label
                                    class="text-foreground mb-1 block text-xs font-medium"
                                    >External Link (Optional)</label
                                >
                                <input
                                    v-model="person.url"
                                    type="url"
                                    :class="fieldClass"
                                    placeholder="https://..."
                                />
                            </div>
                        </div>
                        <div class="pt-1">
                            <label
                                class="flex cursor-pointer items-center gap-2"
                            >
                                <input
                                    v-model="person.visible"
                                    type="checkbox"
                                    class="border-input text-primary focus:ring-ring h-3.5 w-3.5 rounded"
                                />
                                <span class="text-muted-foreground text-xs">
                                    Display publicly on the gallery page
                                </span>
                            </label>
                        </div>
                    </div>

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
            </FormSection>

            <!-- External Link -->
            <FormSection
                title="External Link (Optional)"
                description="Link to external match fixture details or report."
            >
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                            >Link URL</label
                        >
                        <input
                            v-model="form.external_link"
                            type="url"
                            :class="fieldClass"
                            placeholder="https://..."
                        />
                    </div>
                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                            >Button Label</label
                        >
                        <input
                            v-model="form.external_link_label"
                            type="text"
                            :class="fieldClass"
                            placeholder="e.g. Match Report"
                        />
                    </div>
                </div>
            </FormSection>

            <!-- SEO Metadata -->
            <FormSection
                title="Search Engine Optimization"
                description="Custom title and description for search engines."
            >
                <div class="space-y-4">
                    <div>
                        <label
                            class="text-foreground mb-1 block text-sm font-medium"
                            >SEO Title</label
                        >
                        <input
                            v-model="form.seo.title"
                            type="text"
                            :class="fieldClass"
                            placeholder="Defaults to gallery title"
                        />
                    </div>
                    <div>
                        <label
                            class="text-foreground mb-1 block text-sm font-medium"
                            >SEO Description</label
                        >
                        <textarea
                            v-model="form.seo.description"
                            :class="textareaClass"
                            rows="2"
                            placeholder="Defaults to gallery description"
                        />
                    </div>
                </div>
            </FormSection>

            <!-- Page Actions -->
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
