<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search, ShieldCheck } from '@lucide/vue';
import { ref } from 'vue';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';

interface UserItem {
    id: number;
    name: string;
    email: string;
    role: string;
    is_admin: boolean;
    is_system_account: boolean;
    has_two_factor: boolean;
    email_verified: boolean;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedUsers {
    data: UserItem[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

interface RoleOption {
    value: string;
    label: string;
}

const props = defineProps<{
    users: PaginatedUsers;
    filters: {
        search: string;
        role: string;
    };
    roles: RoleOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Overview',
                href: '/dashboard',
            },
            {
                title: 'Users',
                href: '/dashboard/users',
            },
        ],
    },
});

const search = ref(props.filters.search || '');
const selectedRole = ref(props.filters.role || '');

function handleFilter(): void {
    router.get(
        '/dashboard/users',
        {
            search: search.value || undefined,
            role: selectedRole.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

function handleDelete(user: UserItem): void {
    if (!confirm(`Are you sure you want to delete user "${user.name}"?`)) {
        return;
    }

    router.delete(`/dashboard/users/${user.id}`);
}

function getRoleBadgeClass(role: string): string {
    switch (role) {
        case 'dev':
            return 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20 font-semibold';
        case 'admin':
            return 'bg-primary/10 text-primary border-primary/20';
        case 'editor':
            return 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20';
        case 'coach':
            return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20';
        default:
            return 'bg-muted text-muted-foreground border-border';
    }
}

function getRoleLabel(role: string): string {
    const found = props.roles.find((r) => r.value === role);

    return found ? found.label : role;
}
</script>

<template>
    <Head title="Users" />

    <div class="space-y-6">
        <PageHeader
            title="Users"
            description="Manage system administrators, editors, and coaches."
        >
            <Button as-child>
                <Link href="/dashboard/users/create">
                    <Plus class="mr-1.5 h-4 w-4" />
                    Add User
                </Link>
            </Button>
        </PageHeader>

        <!-- Filters -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <Search
                    class="text-muted-foreground absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2"
                />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search by name or email..."
                    class="border-input bg-background shadow-xs focus:border-primary focus:ring-primary w-full rounded-md border py-2 pl-9 pr-3 text-sm outline-none focus:ring-1"
                    @keyup.enter="handleFilter"
                />
            </div>

            <div class="w-full sm:w-48">
                <select
                    v-model="selectedRole"
                    class="border-input bg-background shadow-xs focus:border-primary focus:ring-primary w-full rounded-md border px-3 py-2 text-sm outline-none focus:ring-1"
                    @change="handleFilter"
                >
                    <option value="">All Roles</option>
                    <option
                        v-for="role in roles"
                        :key="role.value"
                        :value="role.value"
                    >
                        {{ role.label }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Users Table -->
        <div
            v-if="users.data.length > 0"
            class="border-border bg-card shadow-xs overflow-hidden rounded-lg border"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-border bg-muted/50 text-muted-foreground border-b text-xs font-semibold uppercase tracking-wider"
                    >
                        <tr>
                            <th class="px-5 py-3">User</th>
                            <th class="px-5 py-3">Role</th>
                            <th class="px-5 py-3">Security</th>
                            <th class="px-5 py-3">Created</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-border divide-y">
                        <tr
                            v-for="user in users.data"
                            :key="user.id"
                            class="hover:bg-muted/40 transition-colors"
                        >
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="bg-primary/10 text-primary flex h-9 w-9 items-center justify-center rounded-full font-bold"
                                    >
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <div
                                            class="text-foreground flex items-center gap-1.5 font-medium"
                                        >
                                            {{ user.name }}
                                            <span
                                                v-if="user.is_system_account"
                                                class="border-border text-muted-foreground rounded border px-1.5 py-0.5 text-[10px]"
                                            >
                                                System
                                            </span>
                                        </div>
                                        <div
                                            class="text-muted-foreground text-xs"
                                        >
                                            {{ user.email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-medium"
                                    :class="getRoleBadgeClass(user.role)"
                                >
                                    {{ getRoleLabel(user.role) }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span
                                        v-if="user.has_two_factor"
                                        class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400"
                                        title="Two-Factor Authentication Enabled"
                                    >
                                        <ShieldCheck class="h-3.5 w-3.5" />
                                        2FA Active
                                    </span>
                                    <span
                                        v-else
                                        class="text-muted-foreground text-xs"
                                    >
                                        2FA Off
                                    </span>
                                </div>
                            </td>

                            <td class="text-muted-foreground px-5 py-4 text-xs">
                                {{ user.created_at || '—' }}
                            </td>

                            <td class="px-5 py-4 text-right">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <Button as-child variant="ghost" size="sm">
                                        <Link
                                            :href="`/dashboard/users/${user.id}/edit`"
                                        >
                                            Edit
                                        </Link>
                                    </Button>

                                    <Button
                                        v-if="!user.is_system_account"
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        @click="handleDelete(user)"
                                    >
                                        Delete
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="users.total > 15"
                class="border-border flex items-center justify-between border-t px-5 py-3 text-xs"
            >
                <div class="text-muted-foreground">
                    Showing {{ users.from }} to {{ users.to }} of
                    {{ users.total }} users
                </div>
                <div class="flex items-center gap-1">
                    <template v-for="(link, i) in users.links" :key="i">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded px-2.5 py-1 transition-colors"
                            :class="
                                link.active
                                    ? 'bg-primary text-primary-foreground font-semibold'
                                    : 'text-muted-foreground hover:bg-muted'
                            "
                        >
                            <span v-html="link.label" />
                        </Link>
                        <span
                            v-else
                            class="text-muted-foreground/40 px-2.5 py-1"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <EmptyState
            v-else
            title="No users found"
            description="No users matched your search criteria."
        >
            <Button as-child>
                <Link href="/dashboard/users/create">
                    <Plus class="mr-1.5 h-4 w-4" />
                    Add User
                </Link>
            </Button>
        </EmptyState>
    </div>
</template>
