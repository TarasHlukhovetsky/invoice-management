<template>
  <div class="min-h-screen bg-slate-50 text-slate-900">
    <header class="border-b border-slate-200 bg-white">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
        <NuxtLink to="/invoices" class="text-lg font-semibold tracking-tight text-slate-900">
          Invoice Management
        </NuxtLink>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
          Accounting workspace
        </span>
      </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
      <slot />
    </main>
  </div>
</template>
