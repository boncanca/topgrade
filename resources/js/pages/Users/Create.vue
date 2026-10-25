<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';

interface RoleOption {
    value: string;
    label: string;
}

defineProps<{
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
            {
                title: 'New User',
                href: '/dashboard/users/create',
            },
        ],
    },
});

const fieldClass =
    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'editor',
});

function handleSubmit(): void {
    form.post('/dashboard/users');
}

function handleCancel(): void {
    router.visit('/dashboard/users');
}
</script>

<template>
    <Head title="Create User" />

    <FormPage
        title="Create User"
        description="Add a new administrator, editor, or staff member."
    >
        <form class="space-y-12" @submit.prevent="handleSubmit">
            <FormSection
                title="Account Details"
                description="Personal name and email address used for login."
            >
                <div class="space-y-6">
                    <div>
                        <label
                            for="user_name"
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Full Name <span class="text-destructive">*</span>
                        </label>
                        <input
                            id="user_name"
                            v-model="form.name"
                            type="text"
                            :class="fieldClass"
                            placeholder="e.g. Alex Morgan"
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
                            for="user_email"
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Email Address
                            <span class="text-destructive">*</span>
                        </label>
                        <input
                            id="user_email"
                            v-model="form.email"
                            type="email"
                            :class="fieldClass"
                            placeholder="alex@topgradelondon.com"
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
                description="Select the system access level and administrative scope for this account."
            >
                <div class="space-y-6">
                    <div>
                        <label
                            for="user_role"
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Assigned Role
                            <span class="text-destructive">*</span>
                        </label>
                        <select
                            id="user_role"
                            v-model="form.role"
                            :class="fieldClass"
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
                title="Security Credentials"
                description="Set an initial password for this account. The user can update it anytime."
            >
                <div class="space-y-6">
                    <div>
                        <label
                            for="user_password"
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Password <span class="text-destructive">*</span>
                        </label>
                        <input
                            id="user_password"
                            v-model="form.password"
                            type="password"
                            :class="fieldClass"
                            autocomplete="new-password"
                            required
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="user_password_confirmation"
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Confirm Password
                            <span class="text-destructive">*</span>
                        </label>
                        <input
                            id="user_password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            :class="fieldClass"
                            autocomplete="new-password"
                            required
                        />
                    </div>
                </div>
            </FormSection>

            <PageActions>
                <Button type="button" variant="outline" @click="handleCancel">
                    Cancel
                </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creating User...' : 'Create User' }}
                </Button>
            </PageActions>
        </form>
    </FormPage>
</template>
