<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import BlockEditor, { type Block } from '@/components/CMS/BlockEditor.vue';
import { index as contentIndex, store as contentStore } from '@/routes/content';

interface ContentType {
    id: number;
    name: string;
}

defineProps<{
    contentTypes: ContentType[];
}>();

const fieldClass =
    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';
const textareaClass = `${fieldClass} min-h-24`;
const monoTextareaClass = `${textareaClass} font-mono`;

const form = useForm({
    content_type_id: '',
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    status: 'draft',
    published_at: '',
    metadata_json: null as null,
    blocks: [] as Block[],
    seo: {
        title: '',
        description: '',
        canonical_url: '',
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
    form.submit(contentStore());
}

function handleCancel(): void {
    router.visit(contentIndex());
}
</script>

<template>
    <FormPage
        title="Create Content"
        description="Add a new page or content entry."
    >
        <form @submit.prevent="handleSubmit" class="space-y-12">
            <FormSection
                title="Basic Information"
                description="Title, slug, and content type for your page."
            >
                <div class="space-y-6">
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
                            Content Type <span class="text-destructive">*</span>
                        </label>
                        <select
                            v-model="form.content_type_id"
                            :class="fieldClass"
                        >
                            <option value="">Select a content type</option>
                            <option
                                v-for="type in contentTypes"
                                :key="type.id"
                                :value="type.id"
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
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
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
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
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
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
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

            <FormSection title="Page Blocks" description="Build your page by adding, configuring, and reordering content blocks.">
                <BlockEditor v-model="form.blocks" />
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
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
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
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
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
                title="Dynamic Data & Items (JSON)"
                description="Custom key-value JSON structure for dynamic page attributes (tagline, stats, custom features array)."
            >
                <div class="space-y-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Metadata JSON Payload
                        </label>
                        <textarea
                            v-model="form.metadata_json_string"
                            rows="8"
                            :class="monoTextareaClass"
                            placeholder='{\n  "tagline": "ALWAYS THE BEST — EST. 2022",\n  "headline": "DEVELOP YOUR FOOTBALL FUTURE"\n}'
                        />
                        <p class="mt-1 text-xs text-muted-foreground">
                            Provide a valid JSON payload to configure dynamic Inertia properties for Volara.
                        </p>
                        <p
                            v-if="form.errors.metadata_json"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.metadata_json }}
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
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
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
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
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
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
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
                <Button type="button" variant="outline" @click="handleCancel">
                    Cancel
                </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creating...' : 'Create Content' }}
                </Button>
            </PageActions>
        </form>
    </FormPage>
</template>
