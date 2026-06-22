<script setup lang="ts">
import { computed, ref } from 'vue'
import type { Invoice } from '~/types/invoice'
import { formatDate, formatDateTime, formatMoney } from '~/utils/formatters'

const route = useRoute()
const api = useInvoiceApi()

const invoiceId = computed(() => String(route.params.id))
const { data, pending, error, refresh } = await useAsyncData(
  `invoice-${invoiceId.value}`,
  () => api.show(invoiceId.value),
)

const invoice = ref<Invoice | null>(data.value?.data ?? null)

watch(data, (response) => {
  invoice.value = response?.data ?? null
})

function onUpdated(updatedInvoice: Invoice) {
  invoice.value = updatedInvoice
}
</script>

<template>
  <section>
    <NuxtLink to="/invoices" class="inline-flex text-sm font-medium text-slate-600 hover:text-slate-900">
      ← До списку інвойсів
    </NuxtLink>

    <div v-if="pending" class="mt-6 rounded-xl border border-slate-200 bg-white p-8 text-center text-slate-500">
      Завантаження інвойсу…
    </div>

    <div v-else-if="error || !invoice" class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-6">
      <h1 class="font-semibold text-rose-900">Не вдалося завантажити інвойс</h1>
      <button class="mt-4 rounded-lg bg-rose-700 px-4 py-2 text-sm font-semibold text-white" @click="refresh()">
        Повторити
      </button>
    </div>

    <template v-else>
      <div class="mt-6 flex flex-wrap items-start justify-between gap-4">
        <div>
          <p class="text-sm font-medium text-slate-500">Інвойс</p>
          <h1 class="mt-1 text-3xl font-bold tracking-tight">{{ invoice.number }}</h1>
        </div>
        <InvoiceStatusBadge :status="invoice.status" />
      </div>

      <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
          <h2 class="text-base font-semibold">Деталі документа</h2>

          <dl class="mt-5 grid gap-x-8 gap-y-5 sm:grid-cols-2">
            <div>
              <dt class="text-sm text-slate-500">Постачальник</dt>
              <dd class="mt-1 font-medium">{{ invoice.supplier_name }}</dd>
            </div>
            <div>
              <dt class="text-sm text-slate-500">Податковий номер</dt>
              <dd class="mt-1 font-medium">{{ invoice.supplier_tax_id }}</dd>
            </div>
            <div>
              <dt class="text-sm text-slate-500">Дата виставлення</dt>
              <dd class="mt-1 font-medium">{{ formatDate(invoice.issue_date) }}</dd>
            </div>
            <div>
              <dt class="text-sm text-slate-500">Оплатити до</dt>
              <dd class="mt-1 font-medium">{{ formatDate(invoice.due_date) }}</dd>
            </div>
            <div>
              <dt class="text-sm text-slate-500">Сума без ПДВ</dt>
              <dd class="mt-1 font-medium">{{ formatMoney(invoice.net_amount, invoice.currency) }}</dd>
            </div>
            <div>
              <dt class="text-sm text-slate-500">ПДВ</dt>
              <dd class="mt-1 font-medium">{{ formatMoney(invoice.vat_amount, invoice.currency) }}</dd>
            </div>
            <div class="sm:col-span-2 rounded-lg bg-slate-50 p-4">
              <dt class="text-sm text-slate-500">Разом з ПДВ</dt>
              <dd class="mt-1 text-2xl font-bold">{{ formatMoney(invoice.gross_amount, invoice.currency) }}</dd>
            </div>
          </dl>

          <p class="mt-6 border-t border-slate-100 pt-4 text-sm text-slate-500">
            Останнє оновлення: {{ formatDateTime(invoice.updated_at) }}
          </p>
        </section>

        <InvoiceEditForm :invoice="invoice" @updated="onUpdated" />
      </div>
    </template>
  </section>
</template>
