<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import {
    ArrowLeft,
    ArrowRight,
    Check,
    Eye,
    Plus,
    Star,
    Trash2,
    Upload,
    X,
} from '@lucide/vue';

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
    if (!input.files || input.files.length === 0) return;

    for (let i = 0; i < input.files.length; i++) {
        const file = input.files[i];
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
    if (toIdx < 0 || toIdx >= localGallery.value.length) return;

    const item = localGallery.value.splice(fromIdx, 1)[0];
    localGallery.value.splice(toIdx, 0, item);

    // Send new order to server
    isReordering.value = true;
    router.post(
        `/dashboard/moments/${props.moment.id}/media/reorder`,
        {
            order: localGallery.value.map((m) => m.id),
        },
        {
            preserveScroll: true,
            onFinish: () => {
                isReordering.value = false;
            },
        }
    );
}

function deleteMedia(item: GalleryItem): void {
    if (!confirm('Remove this photo from the moment gallery?')) return;

    router.delete(`/dashboard/moments/${props.moment.id}/media/${item.id}`, {
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
    form.post(`/dashboard/moments/${props.moment.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            newImagePreviews.value = [];
            form.images = [];
        },
    });
}

function handleCancel(): void {
    router.visit('/dashboard/moments');
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
                <a :href="`/moments/${moment.slug}`" target="_blank" rel="noopener noreferrer">
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
                        <div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-3 flex items-center justify-between">
                            <span>
                                Current Gallery ({{ localGallery.length }} {{ localGallery.length === 1 ? 'photo' : 'photos' }})
                            </span>
                            <span v-if="isReordering" class="text-xs text-primary animate-pulse">
                                Updating order...
                            </span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            <div
                                v-for="(item, idx) in localGallery"
                                :key="item.id"
                                class="group relative rounded-lg overflow-hidden border border-border bg-card shadow-xs flex flex-col"
                                :class="item.is_cover || form.cover_media_id === item.id ? 'ring-2 ring-primary border-primary' : ''"
                            >
                                <div class="relative aspect-4/3 overflow-hidden bg-muted">
                                    <img
                                        :src="item.url"
                                        :alt="item.name"
                                        class="h-full w-full object-cover"
                                    />

                                    <!-- Cover Badge -->
                                    <div
                                        v-if="item.is_cover || form.cover_media_id === item.id"
                                        class="absolute top-2 left-2 rounded bg-primary px-2 py-0.5 text-[10px] font-bold text-primary-foreground shadow-sm flex items-center gap-1"
                                    >
                                        <Star class="h-3 w-3 fill-current" />
                                        COVER
                                    </div>

                                    <!-- Delete Button -->
                                    <button
                                        type="button"
                                        @click="deleteMedia(item)"
                                        class="absolute top-2 right-2 rounded-full bg-black/70 p-1.5 text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-destructive"
                                        title="Delete photo"
                                    >
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </button>
                                </div>

                                <!-- Card Footer: Reorder & Set Cover -->
                                <div class="p-2.5 flex items-center justify-between gap-1 text-xs border-t border-border bg-muted/30">
                                    <!-- Cover Button -->
                                    <button
                                        type="button"
                                        @click="setCover(item)"
                                        class="text-[11px] font-medium rounded px-2 py-1 transition-colors"
                                        :class="item.is_cover || form.cover_media_id === item.id ? 'bg-primary/10 text-primary font-bold' : 'text-muted-foreground hover:bg-muted hover:text-foreground'"
                                    >
                                        {{ item.is_cover || form.cover_media_id === item.id ? 'Cover' : 'Make Cover' }}
                                    </button>

                                    <!-- Reorder Controls -->
                                    <div class="flex items-center gap-1">
                                        <button
                                            type="button"
                                            :disabled="idx === 0"
                                            @click="moveImage(idx, idx - 1)"
                                            class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground disabled:opacity-20"
                                            title="Move left"
                                        >
                                            <ArrowLeft class="h-3.5 w-3.5" />
                                        </button>
                                        <span class="text-[10px] text-muted-foreground px-1 font-mono">
                                            #{{ idx + 1 }}
                                        </span>
                                        <button
                                            type="button"
                                            :disabled="idx === localGallery.length - 1"
                                            @click="moveImage(idx, idx + 1)"
                                            class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground disabled:opacity-20"
                                            title="Move right"
                                        >
                                            <ArrowRight class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="rounded-lg border border-dashed border-border p-6 text-center text-sm text-muted-foreground">
                        No photos currently in this gallery. Upload photos below.
                    </div>

                    <!-- Upload More Photos -->
                    <div class="pt-4 border-t border-border">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-2">
                            Add More Photos
                        </label>
                        <label
                            class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-border p-6 text-center cursor-pointer hover:border-primary/50 hover:bg-muted/30 transition-colors"
                        >
                            <Upload class="h-6 w-6 text-muted-foreground mb-1" />
                            <span class="text-sm font-medium text-foreground">Click to select photos to append</span>
                            <span class="text-xs text-muted-foreground mt-0.5">JPG, PNG, or WebP (up to 15MB each)</span>
                            <input
                                type="file"
                                multiple
                                accept="image/*"
                                class="hidden"
                                @change="onFileSelect"
                            />
                        </label>

                        <!-- Previews of newly selected photos -->
                        <div v-if="newImagePreviews.length > 0" class="mt-4">
                            <div class="text-xs font-semibold text-primary mb-2">
                                Ready to upload: {{ newImagePreviews.length }} new {{ newImagePreviews.length === 1 ? 'photo' : 'photos' }} (click Save Changes to upload)
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                <div
                                    v-for="(preview, idx) in newImagePreviews"
                                    :key="idx"
                                    class="group relative aspect-square rounded-md overflow-hidden border border-border bg-muted"
                                >
                                    <img
                                        :src="preview.url"
                                        :alt="preview.file.name"
                                        class="h-full w-full object-cover"
                                    />
                                    <button
                                        type="button"
                                        @click="removeNewImage(idx)"
                                        class="absolute top-1 right-1 rounded-full bg-black/70 p-1 text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-destructive"
                                        title="Remove"
                                    >
                                        <X class="h-3.5 w-3.5" />
                                    </button>
                                    <div class="absolute bottom-0 inset-x-0 bg-black/60 px-1 py-0.5 text-[10px] text-white truncate">
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
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Title <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            :class="fieldClass"
                            required
                        />
                        <p v-if="form.errors.title" class="mt-1 text-xs text-destructive">
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Slug
                        </label>
                        <input
                            v-model="form.slug"
                            type="text"
                            :class="fieldClass"
                        />
                        <p class="mt-1 text-xs text-muted-foreground">
                            Public URL identifier (/moments/{{ form.slug }})
                        </p>
                        <p v-if="form.errors.slug" class="mt-1 text-xs text-destructive">
                            {{ form.errors.slug }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            :class="textareaClass"
                            rows="3"
                        />
                        <p v-if="form.errors.description" class="mt-1 text-xs text-destructive">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Status <span class="text-destructive">*</span>
                        </label>
                        <select v-model="form.status" :class="fieldClass">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                        <p v-if="form.errors.status" class="mt-1 text-xs text-destructive">
                            {{ form.errors.status }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Published Date & Time
                        </label>
                        <input
                            v-model="form.published_at"
                            type="datetime-local"
                            :class="fieldClass"
                        />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                v-model="form.featured"
                                type="checkbox"
                                class="h-4 w-4 rounded border-input text-primary focus:ring-ring"
                            />
                            <span class="text-sm font-medium text-foreground">
                                Feature this Moment on the Public Moments Hero Carousel
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
                        class="p-4 rounded-md border border-border bg-card space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                Person #{{ idx + 1 }}
                            </span>
                            <button
                                type="button"
                                @click="removePerson(idx)"
                                class="text-xs text-destructive hover:underline flex items-center gap-1"
                            >
                                <Trash2 class="h-3 w-3" />
                                Remove
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-foreground mb-1">Name</label>
                                <input
                                    v-model="person.name"
                                    type="text"
                                    :class="fieldClass"
                                    placeholder="e.g. Coach Michael"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-foreground mb-1">Role / Context</label>
                                <input
                                    v-model="person.role"
                                    type="text"
                                    :class="fieldClass"
                                    placeholder="e.g. Head Coach / U12 Squad"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-foreground mb-1">External Link (Optional)</label>
                                <input
                                    v-model="person.url"
                                    type="url"
                                    :class="fieldClass"
                                    placeholder="https://..."
                                />
                            </div>
                        </div>
                        <div class="pt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    v-model="person.visible"
                                    type="checkbox"
                                    class="h-3.5 w-3.5 rounded border-input text-primary focus:ring-ring"
                                />
                                <span class="text-xs text-muted-foreground">
                                    Display publicly on the Moment page
                                </span>
                            </label>
                        </div>
                    </div>

                    <Button type="button" variant="outline" size="sm" @click="addPerson">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">Link URL</label>
                        <input v-model="form.external_link" type="url" :class="fieldClass" placeholder="https://..." />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">Button Label</label>
                        <input v-model="form.external_link_label" type="text" :class="fieldClass" placeholder="e.g. Match Report" />
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
                        <label class="mb-1 block text-sm font-medium text-foreground">SEO Title</label>
                        <input v-model="form.seo.title" type="text" :class="fieldClass" placeholder="Defaults to moment title" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-foreground">SEO Description</label>
                        <textarea v-model="form.seo.description" :class="textareaClass" rows="2" placeholder="Defaults to moment description" />
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
