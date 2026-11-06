<script setup>
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const passwordUpdated = ref(false)

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

function updatePassword() {
    passwordUpdated.value = false

    form.put('/profile/password', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            passwordUpdated.value = true
        },
    })
}

function logout() {
    router.post('/logout')
}
</script>

<template>
    <Head title="My Profile" />
    <main class="min-h-screen bg-slate-50 px-4 py-8 text-slate-900 dark:bg-slate-950 dark:text-slate-100 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl space-y-6">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <header class="flex flex-col gap-5 border-b border-slate-100 p-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div class="flex min-w-0 items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xl font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600 dark:text-indigo-400">
                                Profile
                            </p>
                            <h1 class="mt-1 truncate text-2xl font-bold text-slate-900 dark:text-white">
                                {{ user.name }}
                            </h1>
                            <p class="mt-1 break-all text-sm text-slate-500 dark:text-slate-400">
                                {{ user.email }}
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-400 dark:hover:bg-rose-950/40"
                        @click="logout"
                    >
                        Logout
                    </button>
                </header>

                <div class="p-5 sm:p-8">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">
                        Account information
                    </h2>

                    <dl class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Full name
                            </dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">
                                {{ user.name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Email address
                            </dt>
                            <dd class="mt-1 break-all text-sm font-semibold text-slate-900 dark:text-slate-100">
                                {{ user.email }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                        Change password
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Confirm your current password before choosing a new one.
                    </p>
                </div>

                <p
                    v-if="passwordUpdated"
                    class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300"
                    role="status"
                >
                    Your password has been updated.
                </p>

                <form class="mt-6 space-y-5" @submit.prevent="updatePassword">
                    <div>
                        <label for="current_password" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Current password
                        </label>
                        <div class="relative">
                            <input
                                id="current_password"
                                v-model="form.current_password"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                required
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pr-16 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-950"
                                :aria-invalid="Boolean(form.errors.current_password)"
                                aria-describedby="current-password-error"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-3 text-sm font-medium text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400"
                                :aria-label="showCurrentPassword ? 'Hide current password' : 'Show current password'"
                                @click="showCurrentPassword = !showCurrentPassword"
                            >
                                {{ showCurrentPassword ? 'Hide' : 'Show' }}
                            </button>
                        </div>
                        <p
                            v-if="form.errors.current_password"
                            id="current-password-error"
                            class="mt-1.5 text-sm text-rose-600 dark:text-rose-400"
                        >
                            {{ form.errors.current_password }}
                        </p>
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            New password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showNewPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pr-16 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-950"
                                :aria-invalid="Boolean(form.errors.password)"
                                aria-describedby="new-password-help new-password-error"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-3 text-sm font-medium text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400"
                                :aria-label="showNewPassword ? 'Hide new password' : 'Show new password'"
                                @click="showNewPassword = !showNewPassword"
                            >
                                {{ showNewPassword ? 'Hide' : 'Show' }}
                            </button>
                        </div>
                        <p id="new-password-help" class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                            Use at least 8 characters.
                        </p>
                        <p
                            v-if="form.errors.password"
                            id="new-password-error"
                            class="mt-1.5 text-sm text-rose-600 dark:text-rose-400"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Confirm new password
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required
                            minlength="8"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-950"
                            :aria-invalid="Boolean(form.errors.password)"
                        />
                    </div>

                    <div class="border-t border-slate-100 pt-5 dark:border-slate-800">
                        <button
                            type="submit"
                            class="inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:focus:ring-offset-slate-900"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Updating...' : 'Update password' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</template>