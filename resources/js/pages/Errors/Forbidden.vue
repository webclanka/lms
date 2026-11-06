<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})
</script>

<template>
    <Head title="Access denied" />

    <main class="mx-auto flex min-h-[60vh] max-w-2xl flex-col items-center justify-center px-6 text-center">
        <p class="text-sm font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">
            Error 403
        </p>

        <h1 class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">
            You don’t have access to this page
        </h1>

        <p class="mt-3 text-slate-600 dark:text-slate-400">
            Your account doesn’t have the required role or permission. If you think this is a mistake, contact an administrator.
        </p>

        <Link
            href="/profile"
            class="mt-6 inline-flex rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
        >
            Go to your profile
        </Link>
    </main>
</template>