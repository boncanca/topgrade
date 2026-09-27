<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import { Plus, Trash2, Upload, X, Image as ImageIcon } from '@lucide/vue';

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
    status: 'draft' as 'draft' | 'published',
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

const imagePreviews = ref<{ file: File; url: string }[]>([]);

function generateSlug(): void {
    if (!form.title) return;
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
        visible: false, // Default to false for youth privacy
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
        imagePreviews.value.push({
            file,
            url: URL.createObjectURL(file),
        });
    }

    input.value = '';
}

function removeSelectedImage(index: number): void {
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

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Galleries',
                href: '/dashboard/galleries',
            },
            {
                title: 'Create',
                href: '/dashboard/galleries/create',
            },
        ],
    },
});
</script>

<template>
    <Head title="New Gallery" />

    <FormPage
        title="New Gallery"
        description="Add a new matchday photo story or training gallery."
    >
        <form @submit.prevent="handleSubmit" class="space-y-10">
            <!-- Basic Information -->
            <FormSection
                title="Basic Information"
                description="Title, slug, and editorial description for the gallery."
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
                            placeholder="e.g. Matchday — TopGrade U12"
                            required
                            @blur="generateSlug"
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
                            placeholder="e.g. matchday-topgrade-u12"
                        />
                        <p class="mt-1 text-xs text-muted-foreground">
                            Public URL identifier (/moments/{{ form.slug || '...' }})
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
                            placeholder="Brief context about this matchday, squad session, or club moment."
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
                description="Control visibility, feature on the hero carousel, and set publication timing."
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
                                Feature this Gallery on the Public Moments Hero Carousel
                            </span>
                        </label>
                        <p class="mt-1 text-xs text-muted-foreground ml-6">
                            Featured galleries rotate on the hero carousel at the top of the /moments page.
                        </p>
                    </div>
                </div>
            </FormSection>

            <!-- Gallery Images Upload -->
            <FormSection
                title="Gallery Images"
                description="Upload 1 to 50+ photos. You can choose the cover photo and reorder them after creation."
            >
                <div class="space-y-4">
                    <!-- Dropzone -->
                    <label
                        class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-border p-8 text-center cursor-pointer hover:border-primary/50 hover:bg-muted/30 transition-colors"
                    >
                        <Upload class="h-8 w-8 text-muted-foreground mb-2" />
                        <span class="text-sm font-medium text-foreground">Click to upload photos</span>
                        <span class="text-xs text-muted-foreground mt-1">Select multiple JPG, PNG, or WebP images (up to 15MB each)</span>
                        <input
                            type="file"
                            multiple
                            accept="image/*"
                            class="hidden"
                            @change="onFileSelect"
                        />
                    </label>

                    <!-- Previews -->
                    <div v-if="imagePreviews.length > 0" class="mt-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-2">
                            Selected {{ imagePreviews.length }} {{ imagePreviews.length === 1 ? 'photo' : 'photos' }}
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                            <div
                                v-for="(preview, idx) in imagePreviews"
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
                                    @click="removeSelectedImage(idx)"
                                    class="absolute top-1 right-1 rounded-full bg-black/70 p-1 text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-destructive"
                                    title="Remove photo"
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
                description="Link out to match fixture details, league table, or external resources."
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Link URL
                        </label>
                        <input
                            v-model="form.external_link"
                            type="url"
                            :class="fieldClass"
                            placeholder="https://..."
                        />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Link Button Label
                        </label>
                        <input
                            v-model="form.external_link_label"
                            type="text"
                            :class="fieldClass"
                            placeholder="e.g. Match Report / League Table"
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
                        <label class="mb-1 block text-sm font-medium text-foreground">SEO Title</label>
                        <input v-model="form.seo.title" type="text" :class="fieldClass" placeholder="Defaults to moment title" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-foreground">SEO Description</label>
                        <textarea v-model="form.seo.description" :class="textareaClass" rows="2" placeholder="Defaults to moment description" />
                    </div>
                </div>
            </FormSection>

            <!-- Actions -->
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
