<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})
</script>

<template>
    <Head title="Page not found" />

    <main class="mx-auto flex min-h-[60vh] max-w-2xl flex-col items-center justify-center px-6 text-center">
        <p class="text-sm font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
            Error 404
        </p>

        <h1 class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">
            We couldn’t find that page
        </h1>

        <p class="mt-3 text-slate-600 dark:text-slate-400">
            The page may have moved, or the link may be incorrect.
        </p>

        <Link
            href="/admin/dashboard"
            class="mt-6 inline-flex rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
        >
            Go to dashboard
        </Link>
    </main>
</template>