import type { InvoiceStatus } from '~/types/invoice'

export function formatMoney(amount: string | number, currency: string): string {
  return new Intl.NumberFormat('uk-UA', {
    style: 'currency',
    currency,
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(Number(amount))
}

export function formatDate(value: string): string {
  return new Intl.DateTimeFormat('uk-UA', {
    dateStyle: 'medium',
  }).format(new Date(`${value}T00:00:00`))
}

export function formatDateTime(value: string): string {
  return new Intl.DateTimeFormat('uk-UA', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value))
}

export const invoiceStatusLabels: Record<InvoiceStatus, string> = {
  pending: 'Очікує',
  approved: 'Схвалено',
  rejected: 'Відхилено',
}
