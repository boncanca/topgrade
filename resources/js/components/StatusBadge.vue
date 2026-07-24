<script setup lang="ts">
interface Props {
  status: 'draft' | 'published' | 'archived' | 'pending' | 'confirmed' | 'cancelled' | 'new' | 'read' | 'responded'
}

defineProps<Props>()

const statusConfig = {
  draft: { bg: 'bg-gray-100', text: 'text-gray-800', label: 'Draft' },
  published: { bg: 'bg-green-100', text: 'text-green-800', label: 'Published' },
  archived: { bg: 'bg-amber-100', text: 'text-amber-800', label: 'Archived' },
  pending: { bg: 'bg-blue-100', text: 'text-blue-800', label: 'Pending' },
  confirmed: { bg: 'bg-green-100', text: 'text-green-800', label: 'Confirmed' },
  cancelled: { bg: 'bg-red-100', text: 'text-red-800', label: 'Cancelled' },
  new: { bg: 'bg-blue-100', text: 'text-blue-800', label: 'New' },
  read: { bg: 'bg-gray-100', text: 'text-gray-800', label: 'Read' },
  responded: { bg: 'bg-green-100', text: 'text-green-800', label: 'Responded' },
}
</script>

<template>
  <span
    :class="[statusConfig[status].bg, statusConfig[status].text, 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium']"
  >
    {{ statusConfig[status].label }}
  </span>
</template>
