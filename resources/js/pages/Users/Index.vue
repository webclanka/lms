<script setup>
import { onBeforeUnmount, reactive, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const page = usePage()

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
    summary: {
        type: Object,
        required: true,
    },
})

const form = reactive({
    search: props.filters.search ?? '',
    role: props.filters.role ?? '',
    sort: props.filters.sort ?? 'created_at',
    direction: props.filters.direction ?? 'desc',
    per_page: String(props.filters.per_page ?? 10),
})

const suggestions = ref([])
const suggestionsLoading = ref(false)
const suggestionsOpen = ref(false)

let suggestionTimer
let suggestionController
let skipNextSuggestionWatch = false

watch(
    () => [form.search, form.role],
    ([search, role]) => {
        if (skipNextSuggestionWatch) {
            skipNextSuggestionWatch = false
            return
        }

        clearTimeout(suggestionTimer)
        suggestionController?.abort()

        const query = search.trim()

        if (query.length < 2) {
            suggestions.value = []
            suggestionsLoading.value = false
            suggestionsOpen.value = false
            return
        }

        suggestionsOpen.value = true
        suggestions.value = []
        suggestionsLoading.value = true

        suggestionTimer = setTimeout(async () => {
            const controller = new AbortController()
            suggestionController = controller

            try {
                const params = new URLSearchParams({ q: query })

                if (role) {
                    params.set('role', role)
                }

                const response = await fetch(
                    `/admin/users/suggestions?${params.toString()}`,
                    {
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                        signal: controller.signal,
                    },
                )

                if (!response.ok) {
                    throw new Error('Could not load user suggestions')
                }

                suggestions.value = await response.json()
            } catch (error) {
                if (error.name !== 'AbortError') {
                    suggestions.value = []
                }
            } finally {
                if (suggestionController === controller) {
                    suggestionsLoading.value = false
                }
            }
        }, 250)
    },
)

function applyFilters() {
    clearTimeout(suggestionTimer)
    suggestionsOpen.value = false

    router.get('/admin/users', {
        search: form.search,
        role: form.role,
        sort: form.sort,
        direction: form.direction,
        per_page: Number(form.per_page),
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

function selectSuggestion(user) {
    clearTimeout(suggestionTimer)
    suggestionController?.abort()

    skipNextSuggestionWatch = true
    form.search = user.name
    suggestions.value = []
    suggestionsOpen.value = false
    suggestionsLoading.value = false

    applyFilters()
}

function clearFilters() {
    form.search = ''
    form.role = ''
    form.sort = 'created_at'
    form.direction = 'desc'
    form.per_page = '10'
    suggestions.value = []
    suggestionsOpen.value = false
    suggestionsLoading.value = false

    applyFilters()
}

function deleteUser(user) {
    if (confirm(`Delete ${user.name}? This action cannot be undone.`)) {
        router.delete(`/admin/users/${user.id}`, {
            preserveScroll: true,
        })
    }
}

onBeforeUnmount(() => {
    clearTimeout(suggestionTimer)
    suggestionController?.abort()
})
</script>

<template>
    <Head title="Users" />

    <div class="w-full text-[#242424] dark:text-slate-100">
        <div class="w-full">
            <!-- User summary -->
            <section class="border border-[#D6D6D6] bg-white dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-[#D6D6D6] px-4 pt-4 dark:border-slate-800 sm:px-6">
                    <h2 class="text-lg font-semibold text-[#242424] dark:text-white">
                        Your organization
                    </h2>

                    <div class="mt-3 flex gap-1 overflow-x-auto" role="tablist" aria-label="Filter users by role">
                        <button
                        v-for="item in [
    { label: 'All users', role: '', count: summary.total_users },
    { label: 'Super Admins', role: 'super-admin', count: summary.super_admins },
    { label: 'Admins', role: 'admin', count: summary.admins },
    { label: 'Instructors', role: 'instructor', count: summary.instructors },
    { label: 'Students', role: 'student', count: summary.students },
    { label: 'Assistants', role: 'assistant', count: summary.assistants },
    { label: 'Auditors', role: 'auditor', count: summary.auditors },
]"
                            :key="item.label"
                            type="button"
                            role="tab"
                            :aria-selected="form.role === item.role"
                            class="flex shrink-0 items-center gap-2 border-b-2 px-3 py-3 text-sm transition"
                            :class="form.role === item.role
                                ? 'border-[#0078D4] font-semibold text-[#242424] dark:text-white'
                                : 'border-transparent text-[#616161] hover:bg-[#F5F5F5] hover:text-[#242424] dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white'"
                            @click="form.role = item.role; applyFilters()"
                        >
                            {{ item.label }}
                            <span
                                class="text-xs"
                                :class="form.role === item.role ? 'text-[#005A9E] dark:text-sky-300' : 'text-[#777777] dark:text-slate-500'"
                            >
                                {{ item.count }}
                            </span>
                        </button>
                    </div>

                    <p class="max-w-3xl py-4 text-sm leading-6 text-[#424242] dark:text-slate-300">
                        Manage who can access your learning platform. Search accounts, filter by role, and review user details.
                    </p>
                </div>

                <!-- Toolbar -->
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#D6D6D6] px-4 py-3 dark:border-slate-800 sm:px-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <Link
                            href="/admin/users/create"
                            class="inline-flex items-center gap-2 rounded-sm px-2 py-2 text-sm text-[#005A9E] transition hover:bg-[#F5F5F5] hover:underline dark:text-sky-300 dark:hover:bg-slate-800"
                        >
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                                <path d="M10 4a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 10 4Z" />
                            </svg>
                            Add user
                        </Link>

                        <span class="hidden h-6 border-l border-[#D6D6D6] dark:border-slate-700 sm:block" aria-hidden="true" />

                        <button
                            type="button"
                            class="rounded-sm px-2 py-2 text-sm text-[#005A9E] transition hover:bg-[#F5F5F5] hover:underline dark:text-sky-300 dark:hover:bg-slate-800"
                            @click="clearFilters"
                        >
                            Reset filters
                        </button>
                    </div>

                    <p class="text-sm text-[#616161] dark:text-slate-400" aria-live="polite">
                        {{ users.total }} {{ users.total === 1 ? 'user' : 'users' }}
                    </p>
                </div>

                <!-- Filters -->
                <form
                    class="grid grid-cols-1 gap-4 bg-white p-4 dark:bg-slate-900 sm:grid-cols-2 sm:px-6 lg:grid-cols-6"
                    @submit.prevent="applyFilters"
                >
                    <div class="sm:col-span-2 lg:col-span-2">
                        <label for="search" class="mb-1.5 block text-sm font-medium text-[#424242] dark:text-slate-300">
                            Search users
                        </label>

                        <div class="relative">
                            <svg
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[#616161] dark:text-slate-400"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M8.5 3a5.5 5.5 0 1 0 3.482 9.757l3.63 3.631a.75.75 0 1 0 1.06-1.06l-3.63-3.631A5.5 5.5 0 0 0 8.5 3ZM4.5 8.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z"
                                    clip-rule="evenodd"
                                />
                            </svg>

                            <input
                                id="search"
                                v-model="form.search"
                                type="search"
                                autocomplete="off"
                                placeholder="Name or email"
                                class="w-full rounded-sm border border-[#8A8886] bg-white py-2 pl-10 pr-3 text-sm text-[#242424] outline-none placeholder:text-[#616161] focus:border-[#0078D4] focus:ring-1 focus:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                                @focus="suggestionsOpen = form.search.trim().length >= 2"
                                @keydown.esc="suggestionsOpen = false"
                            />

                            <div
                                v-if="suggestionsOpen && form.search.trim().length >= 2"
                                class="absolute z-20 mt-1 max-h-64 w-full overflow-auto border border-[#D6D6D6] bg-white py-1 shadow-lg dark:border-slate-700 dark:bg-slate-900"
                                role="listbox"
                                aria-label="User suggestions"
                            >
                                <p
                                    v-if="suggestionsLoading"
                                    class="px-3 py-2 text-sm text-[#616161] dark:text-slate-400"
                                >
                                    Searching users...
                                </p>

                                <template v-else-if="suggestions.length">
                                    <button
                                        v-for="user in suggestions"
                                        :key="user.id"
                                        type="button"
                                        class="block w-full px-3 py-2 text-left transition hover:bg-[#F0F6FC] focus:bg-[#F0F6FC] focus:outline-none dark:hover:bg-slate-800 dark:focus:bg-slate-800"
                                        role="option"
                                        @click="selectSuggestion(user)"
                                    >
                                        <span class="block text-sm font-medium text-[#242424] dark:text-slate-100">
                                            {{ user.name }}
                                        </span>
                                        <span class="block text-xs text-[#616161] dark:text-slate-400">
                                            {{ user.email }}
                                        </span>
                                    </button>
                                </template>

                                <p
                                    v-else
                                    class="px-3 py-2 text-sm text-[#616161] dark:text-slate-400"
                                >
                                    No matching users found.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="role" class="mb-1.5 block text-sm font-medium text-[#424242] dark:text-slate-300">
                            Role
                        </label>
                        <select
                            id="role"
                            v-model="form.role"
                            class="w-full rounded-sm border border-[#8A8886] bg-white px-3 py-2 text-sm text-[#242424] outline-none focus:border-[#0078D4] focus:ring-1 focus:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200"
                        >
                            <option value="">All roles</option>
                            <option v-for="role in roles" :key="role" :value="role">
                                {{ role }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="sort" class="mb-1.5 block text-sm font-medium text-[#424242] dark:text-slate-300">
                            Sort by
                        </label>
                        <select
                            id="sort"
                            v-model="form.sort"
                            class="w-full rounded-sm border border-[#8A8886] bg-white px-3 py-2 text-sm text-[#242424] outline-none focus:border-[#0078D4] focus:ring-1 focus:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200"
                        >
                            <option value="created_at">Date added</option>
                            <option value="name">Name</option>
                            <option value="email">Email</option>
                        </select>
                    </div>

                    <div>
                        <label for="direction" class="mb-1.5 block text-sm font-medium text-[#424242] dark:text-slate-300">
                            Order
                        </label>
                        <select
                            id="direction"
                            v-model="form.direction"
                            class="w-full rounded-sm border border-[#8A8886] bg-white px-3 py-2 text-sm text-[#242424] outline-none focus:border-[#0078D4] focus:ring-1 focus:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200"
                        >
                            <option value="desc">Descending</option>
                            <option value="asc">Ascending</option>
                        </select>
                    </div>

                    <div>
                        <label for="per_page" class="mb-1.5 block text-sm font-medium text-[#424242] dark:text-slate-300">
                            Rows per page
                        </label>
                        <select
                            id="per_page"
                            v-model="form.per_page"
                            class="w-full rounded-sm border border-[#8A8886] bg-white px-3 py-2 text-sm text-[#242424] outline-none focus:border-[#0078D4] focus:ring-1 focus:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200"
                        >
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    <div class="flex items-end sm:col-span-2 lg:col-span-6">
                        <button
                            type="submit"
                            class="rounded-sm bg-[#0078D4] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#106EBE] focus:outline-none focus:ring-2 focus:ring-[#0078D4] focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                        >
                            Apply filters
                        </button>
                    </div>
                </form>

                <!-- Empty state -->
                <div
                    v-if="users.data.length === 0"
                    class="border-t border-[#D6D6D6] bg-white px-6 py-12 text-center dark:border-slate-800 dark:bg-slate-900"
                >
                    <h2 class="text-base font-semibold text-[#242424] dark:text-white">
                        No users found
                    </h2>
                    <p class="mt-1 text-sm text-[#616161] dark:text-slate-400">
                        Try changing your search or filters.
                    </p>
                </div>

                <template v-else>
                    <!-- Mobile list -->
                    <div class="divide-y divide-[#E1E1E1] border-t border-[#D6D6D6] bg-white dark:divide-slate-800 dark:border-slate-800 dark:bg-slate-900 md:hidden">
                        <article
                            v-for="user in users.data"
                            :key="user.id"
                            class="flex items-center justify-between gap-4 px-4 py-4"
                        >
                            <div class="min-w-0">
                                <p class="truncate font-medium text-[#242424] dark:text-slate-100">
                                    {{ user.name }}
                                </p>
                                <p class="mt-1 truncate text-sm text-[#616161] dark:text-slate-400">
                                    {{ user.email }}
                                </p>
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-[#616161] dark:text-slate-400">
                                    <span class="rounded-sm bg-[#E8E8E8] px-2 py-1 capitalize dark:bg-slate-800">
                                        {{ user.role ?? 'No role' }}
                                    </span>
                                    <span>{{ user.created_at }}</span>
                                </div>
                            </div>

                            <Link
                                :href="String(user.id) === String(page.props.auth?.user?.id)
                                    ? '/profile'
                                    : `/admin/users/${user.id}`"
                                class="shrink-0 text-sm font-medium text-[#005A9E] hover:underline dark:text-sky-300"
                            >
                                View
                            </Link>
                        </article>
                    </div>

                    <!-- Desktop table -->
                    <div class="hidden overflow-x-auto border-t border-[#D6D6D6] bg-white dark:border-slate-800 dark:bg-slate-900 md:block">
                        <table class="w-full text-left">
                            <thead class="border-b border-[#D6D6D6] bg-white text-sm text-[#424242] dark:border-slate-800 dark:bg-slate-800 dark:text-slate-300">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Name</th>
                                    <th class="px-6 py-3 font-semibold">Username for sign-in</th>
                                    <th class="px-6 py-3 font-semibold">Role</th>
                                    <th class="px-6 py-3 font-semibold">Date added</th>
                                    <th class="px-6 py-3 text-right font-semibold">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-[#E1E1E1] dark:divide-slate-800">
                                <tr
                                    v-for="user in users.data"
                                    :key="user.id"
                                    class="transition hover:bg-[#F5F5F5] dark:hover:bg-slate-800/60"
                                >
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#0078D4] text-xs font-semibold text-white">
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </span>
                                            <span class="font-medium text-[#242424] dark:text-slate-100">
                                                {{ user.name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-[#424242] dark:text-slate-300">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-sm bg-[#E8E8E8] px-2 py-1 text-xs font-medium capitalize text-[#424242] dark:bg-slate-800 dark:text-slate-300">
                                            {{ user.role ?? 'No role' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-[#616161] dark:text-slate-400">
                                        {{ user.created_at }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Link
                                            :href="String(user.id) === String(page.props.auth?.user?.id)
                                                ? '/profile'
                                                : `/admin/users/${user.id}`"
                                            class="text-sm font-medium text-[#005A9E] hover:underline dark:text-sky-300"
                                        >
                                            View
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <!-- Pagination -->
                <nav
                    v-if="users.links.length > 3"
                    class="flex flex-wrap items-center justify-center gap-1 border-t border-[#D6D6D6] bg-white px-4 py-4 dark:border-slate-800 dark:bg-slate-900"
                    aria-label="Pagination"
                >
                    <template v-for="link in users.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded-sm border px-3 py-2 text-sm transition"
                            :class="link.active
                                ? 'border-[#0078D4] bg-[#0078D4] text-white'
                                : 'border-[#D6D6D6] bg-white text-[#424242] hover:bg-[#F5F5F5] dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800'"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="rounded-sm border border-[#E1E1E1] px-3 py-2 text-sm text-[#999999] dark:border-slate-800 dark:text-slate-600"
                            v-html="link.label"
                        />
                    </template>
                </nav>
            </section>
        </div>
    </div>
</template>