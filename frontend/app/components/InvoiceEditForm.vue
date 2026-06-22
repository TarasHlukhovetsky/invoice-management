<script setup lang="ts">
import {toTypedSchema} from '@vee-validate/zod'
import {useForm} from 'vee-validate'
import {computed, ref, watch} from 'vue'
import {z} from 'zod'
import type {ApiValidationError, Invoice, UpdateInvoicePayload} from '~/types/invoice'
import {formatMoney} from '~/utils/formatters'

const props = defineProps<{
  invoice: Invoice
}>()

const emit = defineEmits<{
  updated: [invoice: Invoice]
}>()

const api = useInvoiceApi()
const serverError = ref<string | null>(null)

const validationSchema = toTypedSchema(z.object({
  net_amount: z.coerce.number().positive('Сума без ПДВ має бути більшою за 0.'),
  vat_amount: z.coerce.number().min(0, 'ПДВ не може бути від’ємним.'),
  due_date: z.string().min(1, 'Оберіть дату оплати.'),
}).superRefine((values, context) => {
  if (values.due_date < props.invoice.issue_date) {
    context.addIssue({
      code: z.ZodIssueCode.custom,
      path: ['due_date'],
      message: 'Дата оплати не може бути раніше дати виставлення.',
    })
  }
}))

const {defineField, handleSubmit, errors, isSubmitting, resetForm, setFieldError} = useForm({
  validationSchema,
  initialValues: {
    net_amount: Number(props.invoice.net_amount),
    vat_amount: Number(props.invoice.vat_amount),
    due_date: props.invoice.due_date,
  },
})

const [netAmount] = defineField('net_amount')
const [vatAmount] = defineField('vat_amount')
const [dueDate] = defineField('due_date')

const grossAmount = computed(() => {
  return Number(netAmount.value || 0) + Number(vatAmount.value || 0)
})

const isEditable = computed(() => props.invoice.status === 'pending')

watch(() => props.invoice, (invoice) => {
  resetForm({
    values: {
      net_amount: Number(invoice.net_amount),
      vat_amount: Number(invoice.vat_amount),
      due_date: invoice.due_date,
    },
  })
  serverError.value = null
}, {deep: true})

const onSubmit = handleSubmit(async (values) => {
  serverError.value = null

  try {
    const payload: UpdateInvoicePayload = {
      net_amount: values.net_amount,
      vat_amount: values.vat_amount,
      gross_amount: Number(grossAmount.value.toFixed(2)),
      due_date: values.due_date,
    }

    const response = await api.update(props.invoice.id, payload)
    emit('updated', response.data)
  } catch (error: unknown) {
    const apiError = error as { data?: ApiValidationError }

    if (apiError.data?.errors) {
      for (const [field, messages] of Object.entries(apiError.data.errors)) {
        setFieldError(field, messages[0] ?? 'Некоректне значення.')
      }
    }

    serverError.value = apiError.data?.message ?? 'Не вдалося зберегти зміни. Спробуйте ще раз.'
  }
})
</script>

<template>
  <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-5 flex items-start justify-between gap-4">
      <div>
        <h2 class="text-base font-semibold">Редагування фінансових даних</h2>
        <p class="mt-1 text-sm text-slate-500">
          Змінюються лише суми та дата оплати.
        </p>
      </div>
      <span v-if="!isEditable" class="text-right text-sm font-medium text-rose-700">
        Редагування недоступне для цього статусу.
      </span>
    </div>

    <form class="space-y-4" @submit="onSubmit">
      <fieldset :disabled="!isEditable || isSubmitting" class="space-y-4 disabled:opacity-60">
        <div class="grid gap-4 sm:grid-cols-2">
          <label class="block">
            <span class="mb-1.5 block text-sm font-medium text-slate-700">Сума без ПДВ</span>
            <input
                v-model="netAmount"
                type="number"
                min="0.01"
                step="0.01"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none transition focus:border-slate-800 focus:ring-2 focus:ring-slate-200"
            >
            <span v-if="errors.net_amount" class="mt-1 block text-sm text-rose-600">{{ errors.net_amount }}</span>
          </label>

          <label class="block">
            <span class="mb-1.5 block text-sm font-medium text-slate-700">ПДВ</span>
            <input
                v-model="vatAmount"
                type="number"
                min="0"
                step="0.01"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none transition focus:border-slate-800 focus:ring-2 focus:ring-slate-200"
            >
            <span v-if="errors.vat_amount" class="mt-1 block text-sm text-rose-600">{{ errors.vat_amount }}</span>
          </label>
        </div>

        <label class="block">
          <span class="mb-1.5 block text-sm font-medium text-slate-700">Дата оплати</span>
          <input
              v-model="dueDate"
              type="date"
              :min="invoice.issue_date"
              class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none transition focus:border-slate-800 focus:ring-2 focus:ring-slate-200"
          >
          <span v-if="errors.due_date" class="mt-1 block text-sm text-rose-600">{{ errors.due_date }}</span>
        </label>

        <div class="rounded-lg bg-slate-50 px-4 py-3">
          <span class="text-sm text-slate-500">Разом з ПДВ</span>
          <p class="mt-1 text-xl font-semibold">{{ formatMoney(grossAmount, invoice.currency) }}</p>
        </div>

        <p v-if="serverError" role="alert" class="rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-700">
          {{ serverError }}
        </p>

        <button
            type="submit"
            class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:bg-slate-400"
        >
          {{ isSubmitting ? 'Збереження…' : 'Зберегти зміни' }}
        </button>
      </fieldset>
    </form>
  </section>
</template>
