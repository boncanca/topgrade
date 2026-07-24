<script setup lang="ts">
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import BlockEditor, { type Block } from '@/components/CMS/BlockEditor.vue';
import {
    destroy as contentDestroy,
    show as contentShow,
    update as contentUpdate,
} from '@/routes/content';

interface ContentType {
    id: number;
    name: string;
}

interface ContentBlock {
    id: number;
    uuid: string;
    type: string;
    payload: Record<string, unknown>;
    settings: Record<string, unknown>;
    sort_order: number;
}

interface Content {
    id: number;
    content_type_id: number | string;
    title: string;
    slug: string;
    excerpt: string | null;
    content: string;
    status: string;
    published_at: string | null;
    blocks: ContentBlock[];
    seo?: {
        title: string | null;
        description: string | null;
        canonical_url: string | null;
    } | null;
}

const props = defineProps<{
    content: Content;
    contentTypes: ContentType[];
}>();

const fieldClass =
    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';
const textareaClass = `${fieldClass} min-h-24`;

// Normalise blocks from the server (ensure payload/settings are objects)
function normaliseBlocks(blocks: ContentBlock[]): Block[] {
    return blocks.map((b) => ({
        uuid: b.uuid,
        type: b.type,
        payload: b.payload ?? {},
        settings: b.settings ?? {},
    }));
}

const form = useForm({
    content_type_id: String(props.content.content_type_id),
    title: props.content.title,
    slug: props.content.slug,
    excerpt: props.content.excerpt ?? '',
    content: props.content.content,
    status: props.content.status,
    published_at: props.content.published_at
        ? props.content.published_at.split('T')[0]
        : '',
    metadata_json: props.content.metadata_json ?? null,
    blocks: normaliseBlocks(props.content.blocks ?? []),
    seo: {
        title: props.content.seo?.title ?? '',
        description: props.content.seo?.description ?? '',
        canonical_url: props.content.seo?.canonical_url ?? '',
    },
});

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

function handleSubmit(): void {
    form.submit(contentUpdate(props.content.id));
}

function handleCancel(): void {
    router.visit(contentShow(props.content.id));
}

function handleDelete(): void {
    if (
        !confirm(
            'Are you sure you want to delete this content? This action cannot be undone.',
        )
    ) {
        return;
    }

    router.delete(contentDestroy.url(props.content.id));
}
</script>

<template>
    <FormPage
        title="Edit Content"
        :description="`Editing: ${props.content.title}`"
    >
        <form @submit.prevent="handleSubmit" class="space-y-12">
            <FormSection
                title="Basic Information"
                description="Title, slug, and content type for your page."
            >
                <div class="space-y-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Content Type <span class="text-destructive">*</span>
                        </label>
                        <select
                            v-model="form.content_type_id"
                            :class="fieldClass"
                        >
                            <option value="">Select a content type</option>
                            <option
                                v-for="type in props.contentTypes"
                                :key="type.id"
                                :value="String(type.id)"
                            >
                                {{ type.name }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.content_type_id"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.content_type_id }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Title <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            :class="fieldClass"
                            placeholder="Enter content title"
                            @blur="generateSlug"
                        />
                        <p
                            v-if="form.errors.title"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Slug <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.slug"
                            type="text"
                            :class="fieldClass"
                            placeholder="auto-generated-from-title"
                        />
                        <p
                            v-if="form.errors.slug"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.slug }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Excerpt
                        </label>
                        <textarea
                            v-model="form.excerpt"
                            rows="3"
                            :class="textareaClass"
                            placeholder="Brief summary of the content"
                        />
                        <p
                            v-if="form.errors.excerpt"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.excerpt }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <!-- Block Builder -->
            <FormSection
                title="Page Blocks"
                description="Build your page by adding, configuring, and reordering content blocks."
            >
                <BlockEditor
                    v-model="form.blocks"
                    :content-id="props.content.id"
                />
                <p v-if="form.errors.blocks" class="mt-2 text-sm text-destructive">
                    {{ form.errors.blocks }}
                </p>
            </FormSection>

            <FormSection
                title="Publishing"
                description="Control publication status and timing."
            >
                <div class="space-y-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Status <span class="text-destructive">*</span>
                        </label>
                        <select v-model="form.status" :class="fieldClass">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                        <p
                            v-if="form.errors.status"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.status }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Published At
                        </label>
                        <input
                            v-model="form.published_at"
                            type="datetime-local"
                            :class="fieldClass"
                        />
                        <p
                            v-if="form.errors.published_at"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.published_at }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <FormSection
                title="SEO"
                description="Search engine optimization metadata."
            >
                <div class="space-y-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            SEO Title
                        </label>
                        <input
                            v-model="form.seo.title"
                            type="text"
                            :class="fieldClass"
                            placeholder="SEO title (typically 50-60 characters)"
                        />
                        <p
                            v-if="form.errors['seo.title']"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors['seo.title'] }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            SEO Description
                        </label>
                        <textarea
                            v-model="form.seo.description"
                            rows="3"
                            :class="textareaClass"
                            placeholder="Meta description (typically 120-160 characters)"
                        />
                        <p
                            v-if="form.errors['seo.description']"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors['seo.description'] }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Canonical URL
                        </label>
                        <input
                            v-model="form.seo.canonical_url"
                            type="url"
                            :class="fieldClass"
                            placeholder="https://example.com/page"
                        />
                        <p
                            v-if="form.errors['seo.canonical_url']"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors['seo.canonical_url'] }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <PageActions>
                <Button
                    type="button"
                    variant="destructive"
                    @click="handleDelete"
                >
                    Delete
                </Button>
                <div class="flex gap-4">
                    <Button
                        type="button"
                        variant="outline"
                        @click="handleCancel"
                    >
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving…' : 'Save Changes' }}
                    </Button>
                </div>
            </PageActions>
        </form>
    </FormPage>
</template>
