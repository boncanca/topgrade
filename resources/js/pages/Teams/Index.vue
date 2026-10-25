<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Edit3,
    Eye,
    Image as ImageIcon,
    Plus,
    Search,
    Shield,
    Trash2,
    Users,
} from '@lucide/vue';
import { ref } from 'vue';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';

interface Player {
    name: string;
    number?: number;
    position?: string;
    role?: string;
}

interface TeamItem {
    id: number;
    name: string;
    slug: string;
    age_group: string;
    stage: string | null;
    description: string | null;
    players: Player[];
    players_count: number;
    visual_variant: string | null;
    sort_order: number;
    is_active: boolean;
    image_url: string | null;
    moment: {
        id: number;
        title: string;
        slug: string;
    } | null;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedTeams {
    data: TeamItem[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

const props = defineProps<{
    teams: PaginatedTeams;
    filters: {
        search: string;
        active: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Overview',
                href: '/dashboard',
            },
            {
                title: 'Teams & Squads',
                href: '/dashboard/teams',
            },
        ],
    },
});

const searchQuery = ref(props.filters.search || '');
const selectedActive = ref(props.filters.active || '');

function applyFilters(): void {
    router.get(
        '/dashboard/teams',
        {
            search: searchQuery.value || undefined,
            active: selectedActive.value !== '' ? selectedActive.value : undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}

function resetFilters(): void {
    searchQuery.value = '';
    selectedActive.value = '';
    applyFilters();
}

function handleDelete(team: TeamItem): void {
    if (confirm(`Are you sure you want to delete the team "${team.name}"?`)) {
        router.delete(`/dashboard/teams/${team.id}`);
    }
}
</script>

<template>
    <Head title="Teams & Squads" />

    <div class="space-y-6">
        <PageHeader
            title="Teams & Squad Rosters"
            description="Manage club youth teams, age groups, squad photos, roster players, and linked gallery albums."
        >
            <template #actions>
                <div class="flex items-center gap-2">
                    <Button variant="outline" as-child>
                        <a href="/teams" target="_blank" rel="noopener noreferrer">
                            <Eye class="mr-2 h-4 w-4" />
                            View Public Page
                        </a>
                    </Button>
                    <Button as-child>
                        <Link href="/dashboard/teams/create">
                            <Plus class="mr-2 h-4 w-4" />
                            Add Team
                        </Link>
                    </Button>
                </div>
            </template>
        </PageHeader>

        <!-- Filters Bar -->
        <div class="bg-card border-border flex flex-col gap-4 rounded-lg border p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative max-w-sm flex-1">
                <Search class="text-muted-foreground absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search teams by name, age group, or stage..."
                    class="border-input bg-background placeholder:text-muted-foreground focus-visible:ring-ring w-full rounded-md border py-2 pl-9 pr-4 text-sm focus-visible:outline-none focus-visible:ring-2"
                    @keyup.enter="applyFilters"
                />
            </div>

            <div class="flex items-center gap-3">
                <select
                    v-model="selectedActive"
                    class="border-input bg-background text-foreground focus-visible:ring-ring rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2"
                    @change="applyFilters"
                >
                    <option value="">All Statuses</option>
                    <option value="1">Active Only</option>
                    <option value="0">Inactive</option>
                </select>

                <Button variant="ghost" size="sm" @click="resetFilters">
                    Reset
                </Button>
            </div>
        </div>

        <!-- Teams Table -->
        <div
            v-if="teams.data.length > 0"
            class="bg-card border-border overflow-hidden rounded-lg border"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-muted/50 text-muted-foreground border-border border-b text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3">Team & Age Group</th>
                            <th class="px-6 py-3">Development Stage</th>
                            <th class="px-6 py-3">Players Roster</th>
                            <th class="px-6 py-3">Linked Gallery</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-border divide-y">
                        <tr
                            v-for="team in teams.data"
                            :key="team.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative h-12 w-16 shrink-0 overflow-hidden rounded-md border border-neutral-800 bg-neutral-900">
                                        <img
                                            v-if="team.image_url"
                                            :src="team.image_url"
                                            :alt="team.name"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                        />
                                        <div v-else class="flex h-full w-full items-center justify-center text-neutral-600">
                                            <Shield class="h-6 w-6" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-foreground font-semibold">
                                            {{ team.name }}
                                        </div>
                                        <div class="text-muted-foreground font-mono text-xs">
                                            {{ team.age_group }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-xs">
                                <span class="bg-muted text-muted-foreground rounded px-2 py-0.5 font-medium">
                                    {{ team.stage || 'General Squad' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-xs">
                                <div class="flex items-center gap-1.5 font-medium">
                                    <Users class="h-3.5 w-3.5 text-amber-500" />
                                    <span>{{ team.players_count }} players</span>
                                </div>
                                <div v-if="team.players_count > 0" class="text-muted-foreground mt-0.5 text-[11px] truncate max-w-[200px]">
                                    {{ team.players.map(p => p.name).slice(0, 3).join(', ') }}{{ team.players_count > 3 ? '...' : '' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-xs">
                                <span v-if="team.moment" class="inline-flex items-center gap-1 text-primary hover:underline">
                                    <ImageIcon class="h-3 w-3" />
                                    <span>{{ team.moment.title }}</span>
                                </span>
                                <span v-else class="text-muted-foreground text-xs italic">
                                    None linked
                                </span>
                            </td>

                            <td class="px-6 py-4 text-xs">
                                <StatusBadge :status="team.is_active ? 'published' : 'draft'" />
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        as-child
                                    >
                                        <Link :href="`/dashboard/teams/${team.id}/edit`">
                                            <Edit3 class="h-3.5 w-3.5 mr-1" />
                                            Edit
                                        </Link>
                                    </Button>

                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        @click="handleDelete(team)"
                                    >
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="teams.links && teams.links.length > 3"
                class="border-border flex items-center justify-between border-t px-6 py-4"
            >
                <div class="text-muted-foreground text-xs">
                    Showing {{ teams.from }} to {{ teams.to }} of {{ teams.total }} teams
                </div>
                <div class="flex items-center gap-1">
                    <template v-for="(link, i) in teams.links" :key="i">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                link.active
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-background hover:bg-muted text-foreground',
                                'border-border inline-flex items-center justify-center rounded-md border px-3 py-1 text-xs font-medium transition-colors',
                            ]"
                        >
                            <span v-html="link.label" />
                        </Link>
                        <span
                            v-else
                            :class="[
                                link.active
                                    ? 'bg-primary text-primary-foreground'
                                    : 'text-muted-foreground cursor-not-allowed opacity-50',
                                'border-border inline-flex items-center justify-center rounded-md border px-3 py-1 text-xs font-medium transition-colors',
                            ]"
                        >
                            <span v-html="link.label" />
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <EmptyState
            v-else
            title="No teams found"
            description="Get started by creating your first youth squad team."
        >
            <Button as-child>
                <Link href="/dashboard/teams/create">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Team
                </Link>
            </Button>
        </EmptyState>
    </div>
</template>
