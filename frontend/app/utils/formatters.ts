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
  const normalizedValue = value.includes('T')
      ? value.replace(/\.(\d{3})\d*Z$/, '.$1Z')
      : `${value}T00:00:00`

  const date = new Date(normalizedValue)

  if (Number.isNaN(date.getTime())) {
    return '—'
  }

  return new Intl.DateTimeFormat('uk-UA', {
    dateStyle: 'medium',
  }).format(date)
}

export function formatDateTime(value: string): string {
  const normalizedValue = value.replace(
      /\.(\d{3})\d*Z$/,
      '.$1Z',
  )

  const date = new Date(normalizedValue)

  if (Number.isNaN(date.getTime())) {
    return '—'
  }

  return new Intl.DateTimeFormat('uk-UA', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(date)
}

export const invoiceStatusLabels: Record<InvoiceStatus, string> = {
  pending: 'Очікує',
  approved: 'Схвалено',
  rejected: 'Відхилено',
}