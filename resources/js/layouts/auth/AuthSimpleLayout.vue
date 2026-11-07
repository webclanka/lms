<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps<{
    title?: string
    description?: string
}>()
</script>

<template>
    <main class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-slate-50 px-4 py-10 text-slate-900 dark:bg-slate-950 dark:text-slate-100 sm:px-6">
        <!-- Soft decorative background -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -left-24 -top-32 h-80 w-80 rounded-full bg-sky-200/50 blur-3xl dark:bg-sky-900/20" />
            <div class="absolute -bottom-36 -right-20 h-96 w-96 rounded-full bg-indigo-200/50 blur-3xl dark:bg-indigo-900/20" />
            <div class="absolute inset-0 bg-[radial-gradient(#94a3b8_0.7px,transparent_0.7px)] [background-size:22px_22px] opacity-20 dark:opacity-[0.08]" />
        </div>

        <div class="relative w-full max-w-md">
            <section class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white/95 shadow-xl shadow-slate-900/5 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95 dark:shadow-black/20">
                <header class="border-b border-slate-100 px-7 pb-6 pt-8 text-center dark:border-slate-800 sm:px-9 sm:pt-9">
                    <Link
                        :href="route('home')"
                        class="group mx-auto inline-flex items-center gap-3 rounded-lg text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-600 focus-visible:ring-offset-4 dark:focus-visible:ring-offset-slate-900"
                        aria-label="Sasinna LMS home"
                    >
                        <span class="flex h-14 w-14 items-center justify-center rounded-xl bg-sky-50 ring-1 ring-sky-100 transition group-hover:scale-105 dark:bg-sky-950/60 dark:ring-sky-900">
                            <img src="/logo.png" alt="" class="h-11 w-11 object-contain" />
                        </span>
                        <span>
                            <span class="block text-lg font-bold tracking-tight text-slate-900 dark:text-white">Sasinna LMS</span>
                            <span class="mt-0.5 block text-xs font-medium uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Learning Management</span>
                        </span>
                    </Link>

                    <div class="mt-7">
                        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">
                            {{ title }}
                        </h1>
                        <p v-if="description" class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-600 dark:text-slate-400">
                            {{ description }}
                        </p>
                    </div>
                </header>

                <div class="px-7 py-7 sm:px-9 sm:py-8">
                    <slot />
                </div>
            </section>

            <footer class="mt-6 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    © {{ new Date().getFullYear() }} Sasinna LMS
                    <span class="mx-1.5 text-slate-300 dark:text-slate-700" aria-hidden="true">·</span>
                    Developed by
                    <a
                        href="https://webclanka.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="font-semibold text-slate-700 underline decoration-slate-300 underline-offset-4 transition hover:text-sky-700 hover:decoration-sky-500 focus:outline-none focus-visible:rounded-sm focus-visible:ring-2 focus-visible:ring-sky-600 dark:text-slate-200 dark:decoration-slate-600 dark:hover:text-sky-300 dark:hover:decoration-sky-400"
                    >
                        Web C Lanka (Pvt) Ltd
                    </a>
                </p>
            </footer>
        </div>
    </main>
</template>
