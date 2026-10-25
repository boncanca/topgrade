<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    Activity,
    AlertCircle,
    ChevronDown,
    ChevronRight,
    Clock,
    RefreshCw,
    Search,
    Trash2,
} from '@lucide/vue';
import { ref } from 'vue';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';

interface AuditItem {
    id: number;
    description: string;
    event: string | null;
    log_name: string | null;
    causer: {
        id: number;
        name: string;
        email: string;
    } | null;
    subject_type: string | null;
    subject_id: number | null;
    properties: Record<string, any>;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedAuditLogs {
    data: AuditItem[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

interface SystemLogItem {
    timestamp: string;
    env: string;
    level: string;
    message: string;
    stack_trace: string | null;
}

const props = defineProps<{
    tab: 'audit' | 'system';
    auditLogs: PaginatedAuditLogs;
    systemLogs: SystemLogItem[];
    systemLogSize: string;
    filters: {
        search: string;
        level: string;
        tab: string;
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
                title: 'Logs & Audits',
                href: '/dashboard/logs',
            },
        ],
    },
});

const currentTab = ref<'audit' | 'system'>(props.tab || 'audit');
const searchQuery = ref(props.filters.search || '');
const selectedLevel = ref(props.filters.level || '');
const expandedSystemLogIndices = ref<Set<number>>(new Set());
const expandedAuditIndices = ref<Set<number>>(new Set());

function switchTab(tab: 'audit' | 'system'): void {
    currentTab.value = tab;
    applyFilters();
}

function applyFilters(): void {
    router.get(
        '/dashboard/logs',
        {
            tab: currentTab.value,
            search: searchQuery.value || undefined,
            level: selectedLevel.value || undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}

function resetFilters(): void {
    searchQuery.value = '';
    selectedLevel.value = '';
    applyFilters();
}

function toggleSystemTrace(index: number): void {
    if (expandedSystemLogIndices.value.has(index)) {
        expandedSystemLogIndices.value.delete(index);
    } else {
        expandedSystemLogIndices.value.add(index);
    }
}

function toggleAuditDetails(index: number): void {
    if (expandedAuditIndices.value.has(index)) {
        expandedAuditIndices.value.delete(index);
    } else {
        expandedAuditIndices.value.add(index);
    }
}

function clearSystemLogs(): void {
    if (
        confirm(
            'Are you sure you want to clear the system log file? This cannot be undone.',
        )
    ) {
        router.post('/dashboard/logs/clear', { type: 'system' });
    }
}

function clearAuditLogs(): void {
    if (
        confirm(
            'Are you sure you want to clear the audit activity records? This will delete past activity logs.',
        )
    ) {
        router.post('/dashboard/logs/clear', { type: 'audit' });
    }
}

function getLevelBadgeClass(level: string): string {
    const upper = level.toUpperCase();

    if (
        upper.includes('ERROR') ||
        upper.includes('CRITICAL') ||
        upper.includes('ALERT') ||
        upper.includes('EMERGENCY')
    ) {
        return 'bg-destructive/10 text-destructive border-destructive/20';
    }

    if (upper.includes('WARNING')) {
        return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20';
    }

    if (upper.includes('INFO') || upper.includes('NOTICE')) {
        return 'bg-primary/10 text-primary border-primary/20';
    }

    return 'bg-muted text-muted-foreground border-border';
}

function formatSubject(type: string | null, id: number | null): string {
    if (!type) {
        return '-';
    }

    const base = type.split('\\').pop() ?? type;

    return id ? `${base} #${id}` : base;
}
</script>

<template>
    <Head title="Logs & Audits" />

    <div class="space-y-6">
        <PageHeader
            title="Logs & Activity Audit"
            description="Track user operations, administrative actions, and retrace system errors or stack traces."
        >
            <template #actions>
                <div class="flex items-center gap-2">
                    <Button
                        v-if="currentTab === 'system'"
                        variant="outline"
                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                        @click="clearSystemLogs"
                    >
                        <Trash2 class="mr-2 h-4 w-4" />
                        Clear System Log ({{ systemLogSize }})
                    </Button>
                    <Button
                        v-else
                        variant="outline"
                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                        @click="clearAuditLogs"
                    >
                        <Trash2 class="mr-2 h-4 w-4" />
                        Clear Audit Trail
                    </Button>
                    <Button variant="outline" @click="applyFilters">
                        <RefreshCw class="mr-2 h-4 w-4" />
                        Refresh
                    </Button>
                </div>
            </template>
        </PageHeader>

        <!-- Navigation Tabs -->
        <div class="border-border border-b">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button
                    type="button"
                    :class="[
                        currentTab === 'audit'
                            ? 'border-primary text-primary font-medium'
                            : 'text-muted-foreground hover:border-border hover:text-foreground border-transparent',
                        'flex items-center gap-2 border-b-2 px-1 py-3 text-sm transition-colors',
                    ]"
                    @click="switchTab('audit')"
                >
                    <Activity class="h-4 w-4" />
                    <span>User Activity Audit</span>
                    <span
                        v-if="auditLogs.total > 0"
                        class="bg-muted text-muted-foreground ml-1.5 rounded-full px-2 py-0.5 text-xs font-semibold"
                    >
                        {{ auditLogs.total }}
                    </span>
                </button>

                <button
                    type="button"
                    :class="[
                        currentTab === 'system'
                            ? 'border-primary text-primary font-medium'
                            : 'text-muted-foreground hover:border-border hover:text-foreground border-transparent',
                        'flex items-center gap-2 border-b-2 px-1 py-3 text-sm transition-colors',
                    ]"
                    @click="switchTab('system')"
                >
                    <AlertCircle class="h-4 w-4" />
                    <span>System Error Logs</span>
                    <span
                        class="bg-muted text-muted-foreground ml-1.5 rounded-full px-2 py-0.5 text-xs font-semibold"
                    >
                        {{ systemLogSize }}
                    </span>
                </button>
            </nav>
        </div>

        <!-- Filter bar -->
        <div
            class="bg-card border-border flex flex-col gap-4 rounded-lg border p-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="relative max-w-sm flex-1">
                <Search
                    class="text-muted-foreground absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2"
                />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search logs by keyword, error or user..."
                    class="border-input bg-background placeholder:text-muted-foreground focus-visible:ring-ring w-full rounded-md border py-2 pl-9 pr-4 text-sm focus-visible:outline-none focus-visible:ring-2"
                    @keyup.enter="applyFilters"
                />
            </div>

            <div class="flex items-center gap-3">
                <select
                    v-if="currentTab === 'system'"
                    v-model="selectedLevel"
                    class="border-input bg-background text-foreground focus-visible:ring-ring rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2"
                    @change="applyFilters"
                >
                    <option value="">All Levels</option>
                    <option value="ERROR">ERROR / CRITICAL</option>
                    <option value="WARNING">WARNING</option>
                    <option value="INFO">INFO / NOTICE</option>
                    <option value="DEBUG">DEBUG</option>
                </select>

                <Button variant="ghost" size="sm" @click="resetFilters">
                    Reset
                </Button>
            </div>
        </div>

        <!-- Tab 1: Audit Activity -->
        <div v-if="currentTab === 'audit'" class="space-y-4">
            <div
                v-if="auditLogs.data.length > 0"
                class="bg-card border-border overflow-hidden rounded-lg border"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-muted/50 text-muted-foreground border-border border-b text-xs uppercase"
                        >
                            <tr>
                                <th class="px-6 py-3">Action & Details</th>
                                <th class="px-6 py-3">Performed By</th>
                                <th class="px-6 py-3">Target Subject</th>
                                <th class="px-6 py-3">Time</th>
                                <th class="px-6 py-3 text-right">Properties</th>
                            </tr>
                        </thead>
                        <tbody class="divide-border divide-y">
                            <template
                                v-for="(item, idx) in auditLogs.data"
                                :key="item.id"
                            >
                                <tr class="hover:bg-muted/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div
                                            class="text-foreground font-medium"
                                        >
                                            {{ item.description }}
                                        </div>
                                        <div
                                            v-if="item.event"
                                            class="text-muted-foreground text-xs"
                                        >
                                            Event:
                                            <span class="font-mono">{{
                                                item.event
                                            }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div
                                            v-if="item.causer"
                                            class="flex items-center gap-2"
                                        >
                                            <div
                                                class="bg-primary/10 text-primary flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold"
                                            >
                                                {{ item.causer.name.charAt(0) }}
                                            </div>
                                            <div>
                                                <div
                                                    class="text-foreground text-xs font-medium"
                                                >
                                                    {{ item.causer.name }}
                                                </div>
                                                <div
                                                    class="text-muted-foreground text-[11px]"
                                                >
                                                    {{ item.causer.email }}
                                                </div>
                                            </div>
                                        </div>
                                        <span
                                            v-else
                                            class="text-muted-foreground text-xs italic"
                                        >
                                            System / Automated
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-muted text-muted-foreground inline-flex items-center rounded-md px-2 py-1 font-mono text-xs"
                                        >
                                            {{
                                                formatSubject(
                                                    item.subject_type,
                                                    item.subject_id,
                                                )
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="text-muted-foreground whitespace-nowrap px-6 py-4 text-xs"
                                    >
                                        {{ item.created_at ?? 'Recently' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Button
                                            v-if="
                                                item.properties &&
                                                Object.keys(item.properties)
                                                    .length > 0
                                            "
                                            variant="ghost"
                                            size="sm"
                                            @click="toggleAuditDetails(idx)"
                                        >
                                            <span class="mr-1 text-xs">
                                                {{
                                                    expandedAuditIndices.has(
                                                        idx,
                                                    )
                                                        ? 'Hide'
                                                        : 'View Payload'
                                                }}
                                            </span>
                                            <ChevronDown
                                                v-if="
                                                    expandedAuditIndices.has(
                                                        idx,
                                                    )
                                                "
                                                class="h-3.5 w-3.5"
                                            />
                                            <ChevronRight
                                                v-else
                                                class="h-3.5 w-3.5"
                                            />
                                        </Button>
                                        <span
                                            v-else
                                            class="text-muted-foreground text-xs"
                                        >
                                            -
                                        </span>
                                    </td>
                                </tr>
                                <!-- Expanded payload -->
                                <tr
                                    v-if="expandedAuditIndices.has(idx)"
                                    class="bg-muted/10"
                                >
                                    <td colspan="5" class="px-6 py-3">
                                        <div
                                            class="bg-background text-foreground border-border overflow-x-auto rounded border p-3 font-mono text-xs"
                                        >
                                            <pre>{{
                                                JSON.stringify(
                                                    item.properties,
                                                    null,
                                                    2,
                                                )
                                            }}</pre>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="auditLogs.links && auditLogs.links.length > 3"
                    class="border-border flex items-center justify-between border-t px-6 py-4"
                >
                    <div class="text-muted-foreground text-xs">
                        Showing {{ auditLogs.from }} to {{ auditLogs.to }} of
                        {{ auditLogs.total }} audit events
                    </div>
                    <div class="flex items-center gap-1">
                        <template v-for="(link, i) in auditLogs.links" :key="i">
                            <a
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
                            </a>
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
                title="No audit activity recorded"
                description="User activities and administrative modifications will appear here in chronological order."
                icon="Activity"
            />
        </div>

        <!-- Tab 2: System Logs -->
        <div v-if="currentTab === 'system'" class="space-y-4">
            <div v-if="systemLogs.length > 0" class="space-y-3">
                <div
                    v-for="(log, idx) in systemLogs"
                    :key="idx"
                    class="bg-card border-border hover:border-border/80 rounded-lg border p-4 transition-all"
                >
                    <div
                        class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div class="flex flex-1 items-start gap-3">
                            <span
                                :class="[
                                    getLevelBadgeClass(log.level),
                                    'shrink-0 rounded border px-2 py-0.5 font-mono text-xs font-semibold uppercase',
                                ]"
                            >
                                {{ log.level }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p
                                    class="text-foreground break-words font-mono text-sm leading-relaxed"
                                >
                                    {{ log.message }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="text-muted-foreground flex shrink-0 items-center gap-3 self-end text-xs sm:self-start"
                        >
                            <span class="flex items-center gap-1 font-mono">
                                <Clock class="h-3.5 w-3.5" />
                                {{ log.timestamp }}
                            </span>
                            <span
                                class="bg-muted rounded px-1.5 py-0.5 text-[10px] font-medium uppercase"
                            >
                                {{ log.env }}
                            </span>
                        </div>
                    </div>

                    <!-- Stack trace toggle -->
                    <div
                        v-if="log.stack_trace"
                        class="border-border/50 mt-3 border-t pt-3"
                    >
                        <Button
                            variant="ghost"
                            size="sm"
                            class="text-muted-foreground hover:text-foreground h-7 px-2 text-xs"
                            @click="toggleSystemTrace(idx)"
                        >
                            <ChevronDown
                                v-if="expandedSystemLogIndices.has(idx)"
                                class="mr-1 h-3.5 w-3.5"
                            />
                            <ChevronRight v-else class="mr-1 h-3.5 w-3.5" />
                            {{
                                expandedSystemLogIndices.has(idx)
                                    ? 'Collapse Stack Trace'
                                    : 'Expand Stack Trace / Retrace Details'
                            }}
                        </Button>

                        <div
                            v-if="expandedSystemLogIndices.has(idx)"
                            class="bg-muted/40 text-foreground border-border mt-2 max-h-96 overflow-y-auto rounded-md border p-3 font-mono text-xs leading-relaxed"
                        >
                            <pre class="whitespace-pre-wrap break-all">{{
                                log.stack_trace
                            }}</pre>
                        </div>
                    </div>
                </div>
            </div>

            <EmptyState
                v-else
                title="System log is clean"
                description="There are currently no errors or logged events matching your filter in storage/logs/laravel.log."
                icon="CheckCircle"
            />
        </div>
    </div>
</template>
