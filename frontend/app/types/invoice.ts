export type InvoiceStatus = 'pending' | 'approved' | 'rejected'

export interface Invoice {
  id: number
  number: string
  supplier_name: string
  supplier_tax_id: string
  net_amount: string
  vat_amount: string
  gross_amount: string
  currency: string
  status: InvoiceStatus
  issue_date: string
  due_date: string
  created_at: string
  updated_at: string
}

export interface PaginationMeta {
  current_page: number
  from: number | null
  last_page: number
  links: Array<{ url: string | null; label: string; active: boolean }>
  path: string
  per_page: number
  to: number | null
  total: number
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: PaginationMeta
  links: {
    first: string | null
    last: string | null
    prev: string | null
    next: string | null
  }
}

export interface ApiResponse<T> {
  data: T
}

export interface UpdateInvoicePayload {
  net_amount: number
  vat_amount: number
  gross_amount: number
  due_date: string
}

export interface ApiValidationError {
  message: string
  errors?: Record<string, string[]>
}
