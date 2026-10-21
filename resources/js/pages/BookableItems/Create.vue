<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import {
    index as activitiesIndex,
    store as activitiesStore,
} from '@/routes/bookable-items';

const fieldClass =
    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';
const textareaClass = `${fieldClass} min-h-24`;

const form = useForm({
    name: '',
    slug: '',
    description: '',
    duration_minutes: 60,
    capacity: 1,
    location: '',
    requires_payment: false,
    price: '',
    currency: 'USD',
    is_active: true,
    booking_label: 'Book Now',
});

function generateSlug(): void {
    if (!form.name) {
        return;
    }

    form.slug = form.name
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}

function handleSubmit(): void {
    form.submit(activitiesStore());
}

function handleCancel(): void {
    router.visit(activitiesIndex());
}
</script>

<template>
    <FormPage
        title="Create Activity"
        description="Add a new activity, session, or offering."
    >
        <form @submit.prevent="handleSubmit" class="space-y-12">
            <FormSection
                title="Basic Information"
                description="Name, slug, and description for your activity."
            >
                <div class="space-y-6">
                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Name <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            :class="fieldClass"
                            placeholder="e.g., Training Session, Trial Class"
                            @blur="generateSlug"
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
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Slug <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.slug"
                            type="text"
                            :class="fieldClass"
                            placeholder="training-session"
                        />
                        <p
                            v-if="form.errors.slug"
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.slug }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            :class="textareaClass"
                            placeholder="Describe what this activity entails..."
                        />
                        <p
                            v-if="form.errors.description"
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <FormSection
                title="Booking Settings"
                description="Duration, capacity, and location details."
            >
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label
                                class="text-foreground mb-2 block text-sm font-medium"
                            >
                                Duration (minutes)
                                <span class="text-destructive">*</span>
                            </label>
                            <input
                                v-model.number="form.duration_minutes"
                                type="number"
                                min="15"
                                step="15"
                                :class="fieldClass"
                            />
                            <p
                                v-if="form.errors.duration_minutes"
                                class="text-destructive mt-1 text-sm"
                            >
                                {{ form.errors.duration_minutes }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="text-foreground mb-2 block text-sm font-medium"
                            >
                                Capacity <span class="text-destructive">*</span>
                            </label>
                            <input
                                v-model.number="form.capacity"
                                type="number"
                                min="1"
                                :class="fieldClass"
                            />
                            <p
                                v-if="form.errors.capacity"
                                class="text-destructive mt-1 text-sm"
                            >
                                {{ form.errors.capacity }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Location <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.location"
                            type="text"
                            :class="fieldClass"
                            placeholder="e.g., Training Field A, Main Hall"
                        />
                        <p
                            v-if="form.errors.location"
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.location }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <FormSection
                title="Pricing"
                description="Whether this activity requires payment."
            >
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <input
                            v-model="form.requires_payment"
                            type="checkbox"
                            class="border-input h-4 w-4 rounded border"
                        />
                        <label class="text-foreground text-sm font-medium">
                            This activity requires payment
                        </label>
                    </div>

                    <div v-if="form.requires_payment" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="text-foreground mb-2 block text-sm font-medium"
                                >
                                    Price
                                    <span class="text-destructive">*</span>
                                </label>
                                <input
                                    v-model.number="form.price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    :class="fieldClass"
                                    placeholder="0.00"
                                />
                                <p
                                    v-if="form.errors.price"
                                    class="text-destructive mt-1 text-sm"
                                >
                                    {{ form.errors.price }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="text-foreground mb-2 block text-sm font-medium"
                                >
                                    Currency
                                    <span class="text-destructive">*</span>
                                </label>
                                <select
                                    v-model="form.currency"
                                    :class="fieldClass"
                                >
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="CAD">CAD</option>
                                    <option value="AUD">AUD</option>
                                </select>
                                <p
                                    v-if="form.errors.currency"
                                    class="text-destructive mt-1 text-sm"
                                >
                                    {{ form.errors.currency }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </FormSection>

            <FormSection
                title="Availability"
                description="Control visibility and booking label."
            >
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="border-input h-4 w-4 rounded border"
                        />
                        <label class="text-foreground text-sm font-medium">
                            This activity is active and available for booking
                        </label>
                    </div>

                    <div>
                        <label
                            class="text-foreground mb-2 block text-sm font-medium"
                        >
                            Booking Button Label
                        </label>
                        <input
                            v-model="form.booking_label"
                            type="text"
                            :class="fieldClass"
                            placeholder="e.g., Book Now, Enroll, Sign Up"
                        />
                        <p
                            v-if="form.errors.booking_label"
                            class="text-destructive mt-1 text-sm"
                        >
                            {{ form.errors.booking_label }}
                        </p>
                    </div>
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
                        {{
                            form.processing ? 'Creating...' : 'Create Activity'
                        }}
                    </Button>
                </div>
            </PageActions>
        </form>
    </FormPage>
</template>
