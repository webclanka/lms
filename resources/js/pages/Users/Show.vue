<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const deleteDialogOpen = ref(false)
const deleting = ref(false)

function deleteUser() {
    deleting.value = true

    router.delete(`/admin/users/${props.user.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            deleteDialogOpen.value = false
        },
        onFinish: () => {
            deleting.value = false
        },
    })
}
</script>

<template>
    <Head title="User Details" />

    <div class="mx-auto w-full max-w-5xl text-[#242424] dark:text-slate-100">
        <Link
            href="/admin/users"
            class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-[#005A9E] transition hover:underline dark:text-sky-300"
        >
            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 1 1 1.06 1.06l-3.22 3.22h10.69A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
            </svg>
            Back to users
        </Link>

        <section class="border border-[#D6D6D6] bg-white dark:border-slate-800 dark:bg-slate-900">
            <header class="flex flex-col gap-5 border-b border-[#D6D6D6] p-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between sm:p-7">
                <div class="flex min-w-0 items-center gap-4">
                    <div
                        v-if="user.profile_photo_url"
                        class="h-14 w-14 shrink-0 overflow-hidden rounded-full bg-[#E8E8E8] dark:bg-slate-800"
                    >
                        <img
                            :src="user.profile_photo_url"
                            :alt="`${user.name}'s profile picture`"
                            class="h-full w-full object-cover"
                        >
                    </div>
                    <div
                        v-else
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#DCEEFF] text-xl font-semibold text-[#005A9E] dark:bg-indigo-950 dark:text-indigo-300"
                    >
                        {{ user.name?.charAt(0).toUpperCase() ?? '?' }}
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-medium text-[#005A9E] dark:text-sky-300">
                            User profile
                        </p>
                        <h1 class="mt-1 truncate text-2xl font-semibold text-[#242424] dark:text-white">
                            {{ user.name }}
                        </h1>
                        <p class="mt-1 break-all text-sm text-[#616161] dark:text-slate-400">
                            {{ user.email }}
                        </p>
                    </div>
                </div>

                <div class="flex shrink-0 flex-wrap gap-2">
                    <Link
                        :href="`/admin/users/${user.id}/edit`"
                        class="inline-flex items-center justify-center gap-2 rounded-sm bg-[#0078D4] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#106EBE] focus:outline-none focus:ring-2 focus:ring-[#0078D4] focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                    >
                        Edit user
                    </Link>

                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-sm border border-[#C8C8C8] bg-white px-4 py-2 text-sm font-semibold text-[#424242] transition hover:bg-[#F5F5F5] focus:outline-none focus:ring-2 focus:ring-[#0078D4] focus:ring-offset-2 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-900"
                        @click="deleteDialogOpen = true"
                    >
                        Delete
                    </button>
                </div>
            </header>

            <div class="p-5 sm:p-7">
                <h2 class="text-base font-semibold text-[#242424] dark:text-white">
                    Account details
                </h2>

                <dl class="mt-5 grid gap-x-8 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-[#616161] dark:text-slate-400">
                            Full name
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-[#242424] dark:text-slate-100">
                            {{ user.name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-[#616161] dark:text-slate-400">
                            Email address
                        </dt>
                        <dd class="mt-1 break-all text-sm font-medium text-[#242424] dark:text-slate-100">
                            {{ user.email }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-[#616161] dark:text-slate-400">
                            Role
                        </dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-sm bg-[#E8E8E8] px-2 py-1 text-xs font-medium capitalize text-[#424242] dark:bg-slate-800 dark:text-slate-300">
                                {{ user.role ?? 'No role assigned' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-[#616161] dark:text-slate-400">
                            Date added
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-[#242424] dark:text-slate-100">
                            {{ user.created_at ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-[#616161] dark:text-slate-400">
                            Last updated
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-[#242424] dark:text-slate-100">
                            {{ user.updated_at ?? '—' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </section>

        <!-- Delete confirmation dialog -->
        <div
            v-if="deleteDialogOpen"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 p-4"
            @keydown.esc="!deleting && (deleteDialogOpen = false)"
        >
            <section
                class="w-full max-w-md border border-[#D6D6D6] bg-white p-6 shadow-2xl dark:border-slate-700 dark:bg-slate-900"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="delete-user-title"
                aria-describedby="delete-user-description"
            >
                <div class="flex h-11 w-11 items-center justify-center rounded-sm bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.584C19.01 16.02 18.042 17.7 16.516 17.7H3.484c-1.526 0-2.494-1.68-1.743-3.017L8.257 3.1ZM10 7a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 7Zm0 7.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                    </svg>
                </div>

                <h2 id="delete-user-title" class="mt-4 text-lg font-semibold text-[#242424] dark:text-white">
                    Delete this user?
                </h2>

                <p id="delete-user-description" class="mt-2 text-sm leading-6 text-[#616161] dark:text-slate-300">
                    You’re about to permanently delete
                    <span class="font-semibold text-[#242424] dark:text-white">{{ user.name }}</span>.
                    This action cannot be undone.
                </p>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        class="inline-flex justify-center rounded-sm border border-[#C8C8C8] bg-white px-4 py-2 text-sm font-semibold text-[#424242] transition hover:bg-[#F5F5F5] disabled:opacity-60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                        :disabled="deleting"
                        @click="deleteDialogOpen = false"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        class="inline-flex justify-center rounded-sm bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="deleting"
                        @click="deleteUser"
                    >
                        {{ deleting ? 'Deleting...' : 'Yes, delete user' }}
                    </button>
                </div>
            </section>
        </div>
    </div>
</template>