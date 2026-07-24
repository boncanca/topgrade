<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import FormPage from '@/components/FormPage.vue';
import FormSection from '@/components/FormSection.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import { index as activitiesIndex } from '@/routes/bookable-items';

interface Activity {
    id: number;
    name: string;
}

defineProps<{
    activity: Activity;
}>();

const fieldClass =
    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';

const form = useForm({
    starts_at: '',
    ends_at: '',
    capacity: null as number | null,
    location: '',
    status: 'active',
});

function getMinDateTime(): string {
    const now = new Date();
    now.setHours(now.getHours() + 1);
    return now.toISOString().slice(0, 16);
}

function setEndTime(): void {
    if (form.starts_at) {
        const startDate = new Date(form.starts_at + ':00');
        startDate.setHours(startDate.getHours() + 1);
        form.ends_at = startDate.toISOString().slice(0, 16);
    }
}

function handleSubmit(): void {
    form.post(route('bookable-items.schedules.store', [props.activity.id]));
}

function handleCancel(): void {
    router.visit(route('bookable-items.schedules.index', [props.activity.id]));
}
</script>

<template>
    <FormPage
        :title="`Create Schedule - ${activity.name}`"
        description="Add a new training session for this activity."
    >
        <form @submit.prevent="handleSubmit" class="space-y-12">
            <FormSection
                title="Schedule Details"
                description="When this session occurs and how many participants it can accommodate."
            >
                <div class="space-y-6">
                    <!-- Start Date/Time -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Starts <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.starts_at"
                            type="datetime-local"
                            :min="getMinDateTime()"
                            :class="fieldClass"
                            @change="setEndTime"
                        />
                        <p v-if="form.errors.starts_at" class="mt-1 text-sm text-destructive">
                            {{ form.errors.starts_at }}
                        </p>
                    </div>

                    <!-- End Date/Time -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Ends <span class="text-destructive">*</span>
                        </label>
                        <input
                            v-model="form.ends_at"
                            type="datetime-local"
                            :min="form.starts_at || getMinDateTime()"
                            :class="fieldClass"
                        />
                        <p v-if="form.errors.ends_at" class="mt-1 text-sm text-destructive">
                            {{ form.errors.ends_at }}
                        </p>
                    </div>

                    <!-- Capacity -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Capacity
                        </label>
                        <input
                            v-model.number="form.capacity"
                            type="number"
                            min="1"
                            :class="fieldClass"
                            placeholder="Leave blank to use activity default"
                        />
                        <p class="mt-1 text-xs text-muted-foreground">
                            Overrides the activity's default capacity for this session
                        </p>
                        <p v-if="form.errors.capacity" class="mt-1 text-sm text-destructive">
                            {{ form.errors.capacity }}
                        </p>
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Location
                        </label>
                        <input
                            v-model="form.location"
                            type="text"
                            :class="fieldClass"
                            placeholder="e.g., Main Pitch, Training Ground A"
                        />
                        <p class="mt-1 text-xs text-muted-foreground">
                            Overrides the activity's default location for this session
                        </p>
                        <p v-if="form.errors.location" class="mt-1 text-sm text-destructive">
                            {{ form.errors.location }}
                        </p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-foreground">
                            Status <span class="text-destructive">*</span>
                        </label>
                        <select v-model="form.status" :class="fieldClass">
                            <option value="active">Active</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="full">Full</option>
                        </select>
                        <p v-if="form.errors.status" class="mt-1 text-sm text-destructive">
                            {{ form.errors.status }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <!-- Actions -->
            <PageActions>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creating...' : 'Create Schedule' }}
                </Button>
                <Button type="button" variant="outline" @click="handleCancel">
                    Cancel
                </Button>
            </PageActions>
        </form>
    </FormPage>
</template>
