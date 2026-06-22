<script setup lang="ts">
import type { InvoiceStatus } from '~/types/invoice'
import { invoiceStatusLabels } from '~/utils/formatters'

defineProps<{
  status: InvoiceStatus
}>()

const classes: Record<InvoiceStatus, string> = {
  pending: 'bg-amber-100 text-amber-800 ring-amber-600/20',
  approved: 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
  rejected: 'bg-rose-100 text-rose-800 ring-rose-600/20',
}
</script>

<template>
  <span
    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset"
    :class="classes[status]"
  >
    {{ invoiceStatusLabels[status] }}
  </span>
</template>
