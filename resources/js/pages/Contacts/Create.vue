<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import { index as contactsIndex, store as contactsStore } from '@/routes/contacts';

const fieldClass =
    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';
const textareaClass = `${fieldClass} min-h-24`;

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    company: '',
    position: '',
    notes: '',
    status: 'active',
});

function handleSubmit(): void {
    form.submit(contactsStore());
}

function handleCancel(): void {
    router.visit(contactsIndex());
}
</script>

<template>
    <FormPage
        title="Add Contact"
        description="Create a new contact record."
    >
        <form @submit.prevent="handleSubmit" class="space-y-12">
            <FormSection
                title="Personal Information"
                description="Contact's basic details."
            >
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-foreground"
                            >
                                First Name <span class="text-destructive">*</span>
                            </label>
                            <input
                                v-model="form.first_name"
                                type="text"
                                :class="fieldClass"
                                placeholder="John"
                            />
                            <p
                                v-if="form.errors.first_name"
                                class="mt-1 text-sm text-destructive"
                            >
                                {{ form.errors.first_name }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-foreground"
                            >
                                Last Name <span class="text-destructive">*</span>
                            </label>
                            <input
                                v-model="form.last_name"
                                type="text"
                                :class="fieldClass"
                                placeholder="Smith"
                            />
                            <p
                                v-if="form.errors.last_name"
                                class="mt-1 text-sm text-destructive"
                            >
                                {{ form.errors.last_name }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
                            Email <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            :class="fieldClass"
                            placeholder="john@example.com"
                        />
                        <p
                            v-if="form.errors.email"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-foreground"
                            >
                                Phone
                            </label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                :class="fieldClass"
                                placeholder="+1 (555) 000-0000"
                            />
                            <p
                                v-if="form.errors.phone"
                                class="mt-1 text-sm text-destructive"
                            >
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-foreground"
                            >
                                Company
                            </label>
                            <input
                                v-model="form.company"
                                type="text"
                                :class="fieldClass"
                                placeholder="Acme Ltd"
                            />
                            <p
                                v-if="form.errors.company"
                                class="mt-1 text-sm text-destructive"
                            >
                                {{ form.errors.company }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-foreground"
                            >
                                Position
                            </label>
                            <input
                                v-model="form.position"
                                type="text"
                                :class="fieldClass"
                                placeholder="Manager / Coach"
                            />
                            <p
                                v-if="form.errors.position"
                                class="mt-1 text-sm text-destructive"
                            >
                                {{ form.errors.position }}
                            </p>
                        </div>
                    </div>
                </div>
            </FormSection>

            <FormSection title="Additional Information">
                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-foreground"
                    >
                        Notes
                    </label>
                    <textarea
                        v-model="form.notes"
                        rows="4"
                        :class="textareaClass"
                        placeholder="Add any notes about this contact..."
                    />
                    <p
                        v-if="form.errors.notes"
                        class="mt-1 text-sm text-destructive"
                    >
                        {{ form.errors.notes }}
                    </p>
                </div>
            </FormSection>

            <FormSection title="Status">
                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-foreground"
                    >
                        Status <span class="text-destructive">*</span>
                    </label>
                    <select v-model="form.status" :class="fieldClass">
                        <option value="active">Active</option>
                        <option value="archived">Archived</option>
                    </select>
                    <p
                        v-if="form.errors.status"
                        class="mt-1 text-sm text-destructive"
                    >
                        {{ form.errors.status }}
                    </p>
                </div>
            </FormSection>

            <PageActions>
                <div class="flex gap-4">
                    <Button
                        type="button"
                        variant="outline"
                        @click="handleCancel"
                    >
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Adding...' : 'Add Contact' }}
                    </Button>
                </div>
            </PageActions>
        </form>
    </FormPage>
</template>
