<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '@/components/PageHeader.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { index as contentIndex, edit as contentEdit } from '@/routes/content';

interface ContentType {
    id: number;
    name: string;
}

interface ContentItem {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    content: string;
    status: 'draft' | 'published' | 'archived';
    published_at: string | null;
    seo?: {
        title: string | null;
        description: string | null;
        canonical_url: string | null;
    } | null;
    contentType: ContentType | null;
}

const props = defineProps<{
    content: ContentItem;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Content',
                href: contentIndex(),
            },
            {
                title: 'View Content',
            },
        ],
    },
});

function formattedDate(value: string | null): string {
    if (!value) {
        return 'Not published';
    }

    return new Intl.DateTimeFormat(undefined, {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}
</script>

<template>
    <Head :title="props.content.title" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.content.title"
            :description="`Slug: /${props.content.slug}`"
        >
            <template #actions>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="contentIndex()">Back</Link>
                    </Button>
                    <Button as-child>
                        <Link :href="contentEdit(props.content.id)"
                            >Edit Content</Link
                        >
                    </Button>
                </div>
            </template>
        </PageHeader>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="space-y-6 md:col-span-2">
                <!-- Main Excerpt & Content -->
                <div
                    class="border-border bg-background space-y-6 rounded-lg border p-6"
                >
                    <div
                        v-if="props.content.excerpt"
                        class="border-primary border-l-4 py-1 pl-4"
                    >
                        <span
                            class="text-muted-foreground block text-xs font-semibold uppercase tracking-wider"
                            >Excerpt</span
                        >
                        <p
                            class="text-foreground mt-1 text-sm font-medium italic"
                        >
                            {{ props.content.excerpt }}
                        </p>
                    </div>

                    <div>
                        <span
                            class="text-muted-foreground mb-3 block text-xs font-semibold uppercase tracking-wider"
                            >Content Body</span
                        >
                        <div
                            class="bg-muted/40 border-border max-h-[500px] overflow-y-auto whitespace-pre-wrap rounded-md border p-4 font-mono text-sm"
                        >
                            {{ props.content.content }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info & SEO -->
            <div class="space-y-6 md:col-span-1">
                <!-- Status & Classification -->
                <div
                    class="border-border bg-background space-y-4 rounded-lg border p-6"
                >
                    <h3
                        class="text-foreground border-border border-b pb-2 text-sm font-semibold"
                    >
                        Status & Meta
                    </h3>
                    <div>
                        <span
                            class="text-muted-foreground block text-xs font-medium uppercase"
                            >Type</span
                        >
                        <span class="text-foreground text-sm">{{
                            props.content.contentType?.name ?? 'Ungrouped'
                        }}</span>
                    </div>
                    <div>
                        <span
                            class="text-muted-foreground mb-1 block text-xs font-medium uppercase"
                            >Status</span
                        >
                        <StatusBadge :status="props.content.status" />
                    </div>
                    <div>
                        <span
                            class="text-muted-foreground block text-xs font-medium uppercase"
                            >Published At</span
                        >
                        <span class="text-foreground text-sm">{{
                            formattedDate(props.content.published_at)
                        }}</span>
                    </div>
                </div>

                <!-- SEO Config -->
                <div
                    class="border-border bg-background space-y-4 rounded-lg border p-6"
                >
                    <h3
                        class="text-foreground border-border border-b pb-2 text-sm font-semibold"
                    >
                        SEO Configuration
                    </h3>
                    <div>
                        <span
                            class="text-muted-foreground block text-xs font-medium uppercase"
                            >SEO Title</span
                        >
                        <span class="text-foreground text-sm font-medium">{{
                            props.content.seo?.title || '(Inherited Title)'
                        }}</span>
                    </div>
                    <div>
                        <span
                            class="text-muted-foreground block text-xs font-medium uppercase"
                            >SEO Description</span
                        >
                        <p class="text-muted-foreground mt-1 text-sm">
                            {{
                                props.content.seo?.description ||
                                'None provided'
                            }}
                        </p>
                    </div>
                    <div>
                        <span
                            class="text-muted-foreground block text-xs font-medium uppercase"
                            >Canonical URL</span
                        >
                        <span
                            class="text-foreground block max-w-full truncate font-mono text-sm text-xs"
                        >
                            {{ props.content.seo?.canonical_url || 'None' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
