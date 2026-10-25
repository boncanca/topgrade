<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';

interface UserData {
    id: number;
    name: string;
    email: string;
    role: string;
    is_system_account: boolean;
}

interface RoleOption {
    value: string;
    label: string;
}

const props = defineProps<{
    user: UserData;
    roles: RoleOption[];
}>();

const page = usePage();
const authUserId = computed(() => (page.props.auth as any)?.user?.id);
const isSelf = computed(() => authUserId.value === props.user.id);

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
            {
                title: 'Edit User',
                href: '#',
            },
        ],
    },
});

const fieldClass =
    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.role,
    password: '',
    password_confirmation: '',
});

function handleSubmit(): void {
    form.put(`/dashboard/users/${props.user.id}`);
}

function handleCancel(): void {
    router.visit('/dashboard/users');
}

function handleDelete(): void {
    if (
        confirm(
            `Are you sure you want to permanently delete ${props.user.name}? This action cannot be undone.`,
        )
    ) {
        router.delete(`/dashboard/users/${props.user.id}`);
    }
}
</script>

<template>
    <Head :title="`Edit ${user.name}`" />

    <FormPage
        :title="`Edit ${user.name}`"
        description="Update account details, role permissions, or reset password."
    >
        <form class="space-y-12" @submit.prevent="handleSubmit">
            <FormSection
                title="Account Details"
                description="Personal name and email address used for login."
            >
                <div class="space-y-6">
                    <div>
                        <label
                            for="edit_name"
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Full Name <span class="text-destructive">*</span>
                        </label>
                        <input
                            id="edit_name"
                            v-model="form.name"
                            type="text"
                            :class="fieldClass"
                            autocomplete="name"
                            required
                        />
                        <p
                            v-if="form.errors.name"
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="edit_email"
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Email Address
                            <span class="text-destructive">*</span>
                        </label>
                        <input
                            id="edit_email"
                            v-model="form.email"
                            type="email"
                            :class="fieldClass"
                            autocomplete="email"
                            required
                        />
                        <p
                            v-if="form.errors.email"
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <FormSection
                title="Role & Permissions"
                description="Manage the account's operational privileges."
            >
                <div class="space-y-6">
                    <div>
                        <label
                            for="edit_role"
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Assigned Role
                            <span class="text-destructive">*</span>
                        </label>
                        <select
                            id="edit_role"
                            v-model="form.role"
                            :class="fieldClass"
                            :disabled="isSelf"
                        >
                            <option
                                v-for="role in roles"
                                :key="role.value"
                                :value="role.value"
                            >
                                {{ role.label }}
                            </option>
                        </select>
                        <p
                            v-if="isSelf"
                            class="text-muted-foreground mt-1 text-xs text-amber-600 dark:text-amber-400"
                        >
                            You cannot change your own role while signed in.
                        </p>
                        <p
                            v-if="form.errors.role"
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.role }}
                        </p>
                        <p class="text-muted-foreground mt-2 text-xs">
                            <span v-if="form.role === 'dev'">
                                <strong>Developer:</strong> Super Administrator with unrestricted access to logs, audit trails, and core system settings.
                            </span>
                            <span v-else-if="form.role === 'admin'">
                                <strong>Administrator:</strong> Full access to
                                users, settings, galleries, articles,
                                activities, and bookings.
                            </span>
                            <span v-else-if="form.role === 'editor'">
                                <strong>Content Editor:</strong> Can publish and
                                edit galleries, articles, and public site
                                contents.
                            </span>
                            <span v-else-if="form.role === 'coach'">
                                <strong>Coach:</strong> Can view assigned
                                activities, schedules, and player bookings.
                            </span>
                        </p>
                    </div>
                </div>
            </FormSection>

            <FormSection
                title="Update Password"
                description="Leave blank to keep the current password unchanged."
            >
                <div class="space-y-6">
                    <div>
                        <label
                            for="edit_password"
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            New Password
                        </label>
                        <input
                            id="edit_password"
                            v-model="form.password"
                            type="password"
                            :class="fieldClass"
                            autocomplete="new-password"
                            placeholder="Optional: Enter only if changing"
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div v-if="form.password">
                        <label
                            for="edit_password_confirmation"
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Confirm New Password
                        </label>
                        <input
                            id="edit_password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            :class="fieldClass"
                            autocomplete="new-password"
                        />
                    </div>
                </div>
            </FormSection>

            <PageActions>
                <div class="flex w-full items-center justify-between">
                    <div>
                        <Button
                            v-if="!user.is_system_account && !isSelf"
                            type="button"
                            variant="destructive"
                            @click="handleDelete"
                        >
                            Delete User
                        </Button>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            @click="handleCancel"
                        >
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{
                                form.processing
                                    ? 'Saving Changes...'
                                    : 'Save Changes'
                            }}
                        </Button>
                    </div>
                </div>
            </PageActions>
        </form>
    </FormPage>
</template>
