<script setup lang="ts">
import { computed, watch } from 'vue'
import { formatDate, formatMoney } from '~/utils/formatters'

const route = useRoute()
const router = useRouter()
const api = useInvoiceApi()

const page = computed(() => {
  const value = Number(route.query.page ?? 1)
  return Number.isInteger(value) && value > 0 ? value : 1
})

const { data, pending, error, refresh } = await useAsyncData(
  'invoices',
  () => api.list(page.value),
  { watch: [page] },
)

function goToPage(nextPage: number) {
  void router.push({
    query: nextPage === 1 ? {} : { page: nextPage },
  })
}

watch(error, () => {
  // The template displays a retry action; this watcher only keeps SSR/client behavior predictable.
})
</script>

<template>
  <section>
    <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
      <div>
        <p class="text-sm font-medium text-slate-500">Документи постачальників</p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight">Інвойси</h1>
      </div>
      <p v-if="data" class="text-sm text-slate-500">
        Усього: {{ data.meta.total }}
      </p>
    </div>

    <div v-if="pending" class="rounded-xl border border-slate-200 bg-white p-8 text-center text-slate-500">
      Завантаження інвойсів…
    </div>

    <div v-else-if="error" class="rounded-xl border border-rose-200 bg-rose-50 p-6">
      <h2 class="font-semibold text-rose-900">Не вдалося завантажити інвойси</h2>
      <p class="mt-1 text-sm text-rose-700">Перевірте, чи запущений API, і спробуйте ще раз.</p>
      <button
        class="mt-4 rounded-lg bg-rose-700 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-800"
        @click="refresh()"
      >
        Повторити
      </button>
    </div>

    <div v-else-if="data" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <div v-if="data.data.length === 0" class="p-8 text-center text-slate-500">
        Інвойсів поки немає.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
              <th class="px-5 py-3">Номер</th>
              <th class="px-5 py-3">Постачальник</th>
              <th class="px-5 py-3">Сума</th>
              <th class="px-5 py-3">Статус</th>
              <th class="px-5 py-3">Оплатити до</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="invoice in data.data"
              :key="invoice.id"
              class="cursor-pointer transition hover:bg-slate-50"
              tabindex="0"
              @click="navigateTo(`/invoices/${invoice.id}`)"
              @keydown.enter="navigateTo(`/invoices/${invoice.id}`)"
            >
              <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                {{ invoice.number }}
              </td>
              <td class="px-5 py-4 text-sm text-slate-700">{{ invoice.supplier_name }}</td>
              <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-900">
                {{ formatMoney(invoice.gross_amount, invoice.currency) }}
              </td>
              <td class="whitespace-nowrap px-5 py-4"><InvoiceStatusBadge :status="invoice.status" /></td>
              <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">{{ formatDate(invoice.due_date) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <nav
        v-if="data.meta.last_page > 1"
        class="flex items-center justify-between border-t border-slate-200 px-5 py-4"
        aria-label="Pagination"
      >
        <button
          class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium disabled:cursor-not-allowed disabled:opacity-50"
          :disabled="page <= 1"
          @click="goToPage(page - 1)"
        >
          Попередня
        </button>
        <span class="text-sm text-slate-600">
          Сторінка {{ data.meta.current_page }} з {{ data.meta.last_page }}
        </span>
        <button
          class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium disabled:cursor-not-allowed disabled:opacity-50"
          :disabled="page >= data.meta.last_page"
          @click="goToPage(page + 1)"
        >
          Наступна
        </button>
      </nav>
    </div>
  </section>
</template>
