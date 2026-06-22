<script setup lang="ts">
defineProps<{
  error: {
    statusCode?: number
    statusMessage?: string
  }
}>()
</script>

<template>
  <NuxtLayout>
    <section class="rounded-xl border border-rose-200 bg-rose-50 p-8">
      <p class="text-sm font-medium text-rose-700">Помилка {{ error.statusCode ?? 500 }}</p>
      <h1 class="mt-1 text-2xl font-bold text-rose-950">
        {{ error.statusMessage ?? 'Щось пішло не так.' }}
      </h1>
      <NuxtLink to="/invoices" class="mt-5 inline-block rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">
        До інвойсів
      </NuxtLink>
    </section>
  </NuxtLayout>
</template>
