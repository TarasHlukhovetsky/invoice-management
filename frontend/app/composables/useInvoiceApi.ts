import type {
  ApiResponse,
  Invoice,
  PaginatedResponse,
  UpdateInvoicePayload,
} from '~/types/invoice'

export function useInvoiceApi() {
  const config = useRuntimeConfig()

  const apiBase = import.meta.server
    ? config.apiBase
    : config.public.apiBase

  function request<T>(path: string, options: Parameters<typeof $fetch<T>>[1] = {}) {
    return $fetch<T>(`${apiBase}${path}`, {
      headers: {
        Accept: 'application/json',
      },
      ...options,
    })
  }

  return {
    list(page = 1) {
      return request<PaginatedResponse<Invoice>>(`/invoices?page=${page}`)
    },

    show(id: string | number) {
      return request<ApiResponse<Invoice>>(`/invoices/${id}`)
    },

    update(id: string | number, payload: UpdateInvoicePayload) {
      return request<ApiResponse<Invoice>>(`/invoices/${id}`, {
        method: 'PUT',
        body: payload,
      })
    },
  }
}
