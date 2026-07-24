<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { index as contactsIndex, edit as contactsEdit } from '@/routes/contacts';
import { index as inquiriesIndex, edit as inquiriesEdit, show as inquiriesShow } from '@/routes/inquiries';

interface Inquiry {
  id: number;
  name: string;
  email: string;
  subject: string;
  status: string;
  created_at: string;
}

interface Booking {
  id: number;
  reference: string;
  status: string;
  scheduled_at: string;
}

interface Contact {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone: string | null;
  company: string | null;
  position: string | null;
  notes: string | null;
  status: string;
  created_at: string;
  inquiries: Inquiry[];
  bookings: Booking[];
}

interface Props {
  contact: Contact;
}

const props = defineProps<Props>();

function formatDate(dateStr: string): string {
  const date = new Date(dateStr);
  return new Intl.DateTimeFormat(undefined, {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  }).format(date);
}

function formatDateTime(dateStr: string): string {
  const date = new Date(dateStr);
  return new Intl.DateTimeFormat(undefined, {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date);
}
</script>

<template>
  <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
    <div class="flex items-center justify-between">
      <PageHeader
        :title="`${props.contact.first_name} ${props.contact.last_name}`"
        :description="props.contact.email"
      />
      <Button as-child variant="default">
        <Link :href="contactsEdit(props.contact.id)">
          Edit
        </Link>
      </Button>
    </div>

    <!-- Contact Details -->
    <div class="grid gap-6 md:grid-cols-2">
      <div class="rounded-lg border border-border bg-card p-6">
        <h3 class="mb-4 font-semibold">Contact Information</h3>
        <div class="space-y-3 text-sm">
          <div>
            <p class="text-muted-foreground">Email</p>
            <p class="font-medium">{{ props.contact.email }}</p>
          </div>
          <div v-if="props.contact.phone">
            <p class="text-muted-foreground">Phone</p>
            <p class="font-medium">{{ props.contact.phone }}</p>
          </div>
          <div v-if="props.contact.company">
            <p class="text-muted-foreground">Company</p>
            <p class="font-medium">{{ props.contact.company }}</p>
          </div>
          <div v-if="props.contact.position">
            <p class="text-muted-foreground">Position</p>
            <p class="font-medium">{{ props.contact.position }}</p>
          </div>
          <div>
            <p class="text-muted-foreground">Status</p>
            <span
              class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="
                props.contact.status === 'active'
                  ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                  : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
              "
            >
              {{ props.contact.status }}
            </span>
          </div>
          <div>
            <p class="text-muted-foreground">Added</p>
            <p class="font-medium">{{ formatDate(props.contact.created_at) }}</p>
          </div>
        </div>
      </div>

      <div v-if="props.contact.notes" class="rounded-lg border border-border bg-card p-6">
        <h3 class="mb-4 font-semibold">Notes</h3>
        <p class="whitespace-pre-wrap text-sm text-foreground">
          {{ props.contact.notes }}
        </p>
      </div>
    </div>

    <!-- Inquiries -->
    <div>
      <h3 class="mb-4 text-lg font-semibold">Inquiries</h3>
      <div
        v-if="props.contact.inquiries.length > 0"
        class="overflow-hidden rounded-lg border border-border"
      >
        <div class="space-y-2 p-4">
          <div
            v-for="inquiry in props.contact.inquiries"
            :key="inquiry.id"
            class="flex items-center justify-between rounded-lg border border-border p-4 hover:bg-muted/30"
          >
            <div class="flex-1">
              <p class="font-medium">{{ inquiry.subject }}</p>
              <p class="mt-1 text-xs text-muted-foreground">
                From: {{ inquiry.name }} ({{ inquiry.email }})
              </p>
              <p class="text-xs text-muted-foreground">
                {{ formatDate(inquiry.created_at) }}
              </p>
            </div>
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="{
                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': inquiry.status === 'new',
                'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200': ['open', 'in_progress'].includes(inquiry.status),
                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': ['resolved', 'closed'].includes(inquiry.status),
                'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': inquiry.status === 'archived',
              }"
            >
              {{ inquiry.status }}
            </span>
            <Button variant="ghost" size="sm" as-child class="ml-2">
              <Link :href="inquiriesShow(inquiry.id)">
                View
              </Link>
            </Button>
          </div>
        </div>
      </div>
      <div v-else class="rounded-lg border border-border border-dashed p-6 text-center text-sm text-muted-foreground">
        No inquiries from this contact
      </div>
    </div>

    <!-- Bookings -->
    <div>
      <h3 class="mb-4 text-lg font-semibold">Bookings</h3>
      <div
        v-if="props.contact.bookings.length > 0"
        class="overflow-hidden rounded-lg border border-border"
      >
        <div class="space-y-2 p-4">
          <div
            v-for="booking in props.contact.bookings"
            :key="booking.id"
            class="flex items-center justify-between rounded-lg border border-border p-4 hover:bg-muted/30"
          >
            <div class="flex-1">
              <p class="font-medium">{{ booking.reference }}</p>
              <p class="text-xs text-muted-foreground">
                Scheduled: {{ formatDateTime(booking.scheduled_at) }}
              </p>
            </div>
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="{
                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': booking.status === 'pending',
                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': booking.status === 'confirmed',
                'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': booking.status === 'cancelled',
              }"
            >
              {{ booking.status }}
            </span>
          </div>
        </div>
      </div>
      <div v-else class="rounded-lg border border-border border-dashed p-6 text-center text-sm text-muted-foreground">
        No bookings from this contact
      </div>
    </div>

    <!-- Back Button -->
    <Button as-child variant="outline">
      <Link :href="contactsIndex()">
        Back to Contacts
      </Link>
    </Button>
  </div>
</template>
