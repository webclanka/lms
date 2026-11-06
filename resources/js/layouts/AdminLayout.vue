<script setup>
import { computed, onMounted, ref } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

const page = usePage()

const currentPageTitle = computed(() => {
    const path = page.url.split('?')[0]

    if (path === '/dashboard') return 'Dashboard'
    if (path === '/admin/users') return 'Users'
    if (path === '/admin/users/create') return 'Create user'
    if (/^\/admin\/users\/[^/]+\/edit$/.test(path)) return 'Edit user'
    if (/^\/admin\/users\/[^/]+$/.test(path)) return 'User details'
    if (path === '/profile') return 'Profile'

    return 'Page'
})

const settingsMenuOpen = ref(false)
const mobileMenuOpen = ref(false)
const sidebarCollapsed = ref(false)
const profileMenuOpen = ref(false)
const darkMode = ref(false)

const navigation = [
    {
        label: 'Dashboard',
        href: '/dashboard',
        icon: 'dashboard',
    },
    {
        label: 'Users',
        href: '/admin/users',
        icon: 'users',
    },
]

const upcomingNavigation = ['Courses', 'Reports']

onMounted(() => {
    darkMode.value = localStorage.getItem('theme') === 'dark'
    applyTheme()
})

function applyTheme() {
    document.documentElement.classList.toggle('dark', darkMode.value)
    document.documentElement.style.colorScheme = darkMode.value ? 'dark' : 'light'
    localStorage.setItem('theme', darkMode.value ? 'dark' : 'light')
}

function toggleTheme() {
    darkMode.value = !darkMode.value
    applyTheme()
}

function isActive(item) {
    if (item.href === '/admin/users') {
        return page.url.startsWith('/admin/users')
    }

    return page.url.startsWith(item.href)
}

function closeMobileMenu() {
    mobileMenuOpen.value = false
}

function logout() {
    profileMenuOpen.value = false
    router.post('/logout')
}

function userInitial() {
    const name = page.props.auth?.user?.name ?? 'Administrator'
    return name.charAt(0).toUpperCase()
}
</script>

<template>
    <div class="min-h-screen bg-[#EBEBEB] text-[#242424] dark:bg-slate-950 dark:text-slate-100">
        <!-- Mobile backdrop -->
        <button
            v-if="mobileMenuOpen"
            type="button"
            class="fixed inset-0 z-40 cursor-default bg-black/30 lg:hidden"
            aria-label="Close navigation menu"
            @click="closeMobileMenu"
        />

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-[#D6D6D6] bg-[#F0F0F0] transition-all duration-200 dark:border-slate-800 dark:bg-slate-900"
            :class="[
                mobileMenuOpen ? 'translate-x-0' : '-translate-x-full',
                sidebarCollapsed ? 'lg:w-20' : 'lg:w-72',
                'lg:translate-x-0',
            ]"
        >
            <!-- Brand and collapse controls -->
            <div class="flex h-14 shrink-0 items-center justify-between border-b border-[#D6D6D6] px-4 dark:border-slate-800">
                <Link
                    href="/dashboard"
                    class="flex min-w-0 items-center gap-3"
                    title="Sasinna LMS"
                    @click="closeMobileMenu"
                >
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-[#0078D4] text-white">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                            <path
                                d="M4 5.5A1.5 1.5 0 0 1 5.5 4h13A1.5 1.5 0 0 1 20 5.5v13a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 18.5v-13Z"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />
                            <path
                                d="M8 8h8M8 12h8M8 16h4"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>

                    <span class="min-w-0" :class="{ 'lg:hidden': sidebarCollapsed }">
                        <span class="block text-sm font-semibold text-[#242424] dark:text-white">
                            Sasinna
                        </span>
                        <span class="block text-xs text-[#616161] dark:text-slate-400">
                            LMS Admin
                        </span>
                    </span>
                </Link>

                <button
                    type="button"
                    class="hidden rounded-sm p-2 text-[#616161] transition hover:bg-[#E2E2E2] hover:text-[#242424] dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white lg:block"
                    :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                    :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                    @click="sidebarCollapsed = !sidebarCollapsed"
                >
                    <svg
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="h-5 w-5 transition-transform"
                        :class="{ 'rotate-180': !sidebarCollapsed }"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.22 4.22a.75.75 0 0 1 1.06 0l5 5a.75.75 0 0 1 0 1.06l-5 5a.75.75 0 1 1-1.06-1.06L11.69 10 7.22 5.28a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>

                <button
                    type="button"
                    class="rounded-sm p-2 text-[#616161] transition hover:bg-[#E2E2E2] dark:text-slate-400 dark:hover:bg-slate-800 lg:hidden"
                    aria-label="Close navigation menu"
                    @click="closeMobileMenu"
                >
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path d="M4.22 4.22a.75.75 0 0 1 1.06 0L10 8.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L11.06 10l4.72 4.72a.75.75 0 0 1-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 1 1-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 0 1 0-1.06Z" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 overflow-y-auto px-2 py-4">
                <p
                    class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-[#616161] dark:text-slate-400"
                    :class="{ 'lg:hidden': sidebarCollapsed }"
                >
                    Workspace
                </p>

                <Link
                    v-for="item in navigation"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-sm border-l-2 px-3 py-2.5 text-sm transition"
                    :class="[
                        isActive(item)
                            ? 'border-[#0078D4] bg-[#E3E3E3] font-semibold text-[#005A9E] dark:bg-slate-800 dark:text-sky-300'
                            : 'border-transparent font-medium text-[#424242] hover:bg-[#E5E5E5] hover:text-[#242424] dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white',
                        sidebarCollapsed ? 'lg:justify-center lg:px-2' : '',
                    ]"
                    :title="sidebarCollapsed ? item.label : undefined"
                    :aria-current="isActive(item) ? 'page' : undefined"
                    @click="closeMobileMenu"
                >
                    <svg
                        v-if="item.icon === 'dashboard'"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="h-5 w-5 shrink-0"
                        aria-hidden="true"
                    >
                        <path d="M3 3.75A.75.75 0 0 1 3.75 3h4.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-.75.75h-4.5A.75.75 0 0 1 3 8.25v-4.5ZM11 3.75a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v2.5a.75.75 0 0 1-.75.75h-4.5a.75.75 0 0 1-.75-.75v-2.5ZM3 12.25a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v4a.75.75 0 0 1-.75.75h-4.5A.75.75 0 0 1 3 16.25v-4ZM11 9.25a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v7a.75.75 0 0 1-.75.75h-4.5a.75.75 0 0 1-.75-.75v-7Z" />
                    </svg>

                    <svg
                        v-else
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="h-5 w-5 shrink-0"
                        aria-hidden="true"
                    >
                        <path d="M10 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8ZM3 16a7 7 0 0 1 14 0v1H3v-1Z" />
                    </svg>

                    <span :class="{ 'lg:hidden': sidebarCollapsed }">
                        {{ item.label }}
                    </span>
                </Link>

                <p
                    class="px-3 pb-2 pt-7 text-xs font-semibold uppercase tracking-wide text-[#616161] dark:text-slate-400"
                    :class="{ 'lg:hidden': sidebarCollapsed }"
                >
                    LMS
                </p>

                <div
                    v-for="item in upcomingNavigation"
                    :key="item"
                    class="flex cursor-not-allowed items-center justify-between rounded-sm px-3 py-2.5 text-sm text-[#777777] dark:text-slate-500"
                    :class="{ 'lg:justify-center lg:px-2': sidebarCollapsed }"
                    :title="`${item} management is coming soon`"
                >
                    <span class="flex items-center gap-3">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5 shrink-0" aria-hidden="true">
                            <path d="M3.5 3A1.5 1.5 0 0 0 2 4.5v11A1.5 1.5 0 0 0 3.5 17h13a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 16.5 3h-13ZM5 6h10v1.5H5V6Zm0 3.25h10v1.5H5v-1.5Zm0 3.25h6V14H5v-1.5Z" />
                        </svg>
                        <span :class="{ 'lg:hidden': sidebarCollapsed }">
                            {{ item }}
                        </span>
                    </span>

                    <span
                        class="rounded-sm bg-[#E5E5E5] px-1.5 py-0.5 text-[10px] font-semibold uppercase text-[#616161] dark:bg-slate-800 dark:text-slate-400"
                        :class="{ 'lg:hidden': sidebarCollapsed }"
                    >
                        Soon
                    </span>
                </div>
            </nav>

            <!-- Sidebar footer -->
            <div
                class="border-t border-[#D6D6D6] p-3 dark:border-slate-800"
                :class="{ 'lg:p-2': sidebarCollapsed }"
            >
                <div
                    class="bg-[#E8E8E8] p-3 dark:bg-slate-800"
                    :class="{ 'lg:hidden': sidebarCollapsed }"
                >
                    <p class="text-xs font-semibold text-[#424242] dark:text-slate-200">
                        Your learning platform
                    </p>
                    <p class="mt-1 text-xs text-[#616161] dark:text-slate-400">
                        Developed by:
                        <a
                            href="https://webclanka.com"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-[#005A9E] hover:underline dark:text-sky-300"
                        >
                            Web C Lanka
                        </a>
                    </p>
                </div>

                <div
                    v-if="sidebarCollapsed"
                    class="hidden justify-center lg:flex"
                    title="LMS Admin"
                >
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#DCEEFF] text-sm font-semibold text-[#005A9E] dark:bg-indigo-950 dark:text-indigo-300">
                        A
                    </span>
                </div>
            </div>
        </aside>

        <!-- Main area -->
        <div
            class="transition-[padding] duration-200"
            :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72'"
        >
            <!-- Top bar -->
            <header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-[#D6D6D6] bg-[#F5F5F5] px-4 dark:border-slate-800 dark:bg-slate-900 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button
                        type="button"
                        class="rounded-sm p-2 text-[#616161] transition hover:bg-[#E5E5E5] dark:text-slate-300 dark:hover:bg-slate-800 lg:hidden"
                        aria-label="Open navigation menu"
                        :aria-expanded="mobileMenuOpen"
                        @click="mobileMenuOpen = true"
                    >
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 5.75A.75.75 0 0 1 3.75 5h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 5.75ZM3 10a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 10Zm.75 3.5a.75.75 0 0 0 0 1.5h12.5a.75.75 0 0 0 0-1.5H3.75Z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <nav class="flex min-w-0 items-center gap-2 text-sm" aria-label="Breadcrumb">
                        <Link
                            href="/dashboard"
                            class="inline-flex shrink-0 items-center gap-2 rounded-sm text-[#616161] transition hover:text-[#005A9E] dark:text-slate-400 dark:hover:text-sky-300"
                            aria-label="Dashboard home"
                            title="Dashboard home"
                        >
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-3v-4H8v4H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" />
                            </svg>
                            <span class="hidden sm:inline">Dashboard</span>
                        </Link>

                        <template v-if="currentPageTitle !== 'Dashboard'">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0 text-[#888888]" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.24a.75.75 0 0 1 0 1.06l-4.27 4.25a.75.75 0 0 1-1.06-.01Z" clip-rule="evenodd" />
                            </svg>

                            <span class="truncate font-medium text-[#424242] dark:text-slate-200" aria-current="page">
                                {{ currentPageTitle }}
                            </span>
                        </template>
                    </nav>
                </div>

                <div class="flex shrink-0 items-center gap-1 sm:gap-2">
                    <!-- Theme toggle -->
                    <button
                        type="button"
                        class="rounded-sm p-2 text-[#616161] transition hover:bg-[#E5E5E5] focus:outline-none focus:ring-2 focus:ring-[#0078D4] dark:text-slate-300 dark:hover:bg-slate-800"
                        :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                        :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                        @click="toggleTheme"
                    >
                        <svg
                            v-if="darkMode"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <path d="M10 2a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-1.5 0v-.5A.75.75 0 0 1 10 2Zm0 13a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-1.5 0v-.5A.75.75 0 0 1 10 15Zm8-5a.75.75 0 0 1-.75.75h-.5a.75.75 0 0 1 0-1.5h.5A.75.75 0 0 1 18 10ZM3.25 10a.75.75 0 0 1-.75.75H2a.75.75 0 0 1 0-1.5h.5a.75.75 0 0 1 .75.75Zm12.407-5.657a.75.75 0 0 1 0 1.06l-.354.354a.75.75 0 1 1-1.06-1.06l.353-.354a.75.75 0 0 1 1.06 0ZM5.757 13.84a.75.75 0 0 1 0 1.06l-.354.354a.75.75 0 0 1-1.06-1.06l.354-.354a.75.75 0 0 1 1.06 0Zm9.9 1.414a.75.75 0 0 1-1.06 0l-.354-.354a.75.75 0 1 1 1.06-1.06l.354.354a.75.75 0 0 1 0 1.06ZM5.757 6.16a.75.75 0 0 1-1.06 0l-.354-.354a.75.75 0 0 1 1.06-1.06l.354.354a.75.75 0 0 1 0 1.06ZM10 6a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z" />
                        </svg>

                        <svg
                            v-else
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <path d="M17.293 13.293A8 8 0 0 1 6.707 2.707a8.001 8.001 0 1 0 10.586 10.586Z" />
                        </svg>
                    </button>

                    <!-- Settings menu -->
                    <div class="relative">
                        <button
                            type="button"
                            class="rounded-sm p-2 text-[#616161] transition hover:bg-[#E5E5E5] focus:outline-none focus:ring-2 focus:ring-[#0078D4] dark:text-slate-300 dark:hover:bg-slate-800"
                            :aria-expanded="settingsMenuOpen"
                            aria-haspopup="menu"
                            aria-label="Open settings"
                            title="Settings"
                            @click="settingsMenuOpen = !settingsMenuOpen; profileMenuOpen = false"
                        >
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path
                                    fill-rule="evenodd"
                                    d="M8.68 2.61a1.5 1.5 0 0 1 2.64 0l.33.59a1.5 1.5 0 0 0 1.65.71l.65-.15a1.5 1.5 0 0 1 1.86 1.86l-.15.65a1.5 1.5 0 0 0 .71 1.65l.59.33a1.5 1.5 0 0 1 0 2.64l-.59.33a1.5 1.5 0 0 0-.71 1.65l.15.65a1.5 1.5 0 0 1-1.86 1.86l-.65-.15a1.5 1.5 0 0 0-1.65.71l-.33.59a1.5 1.5 0 0 1-2.64 0l-.33-.59a1.5 1.5 0 0 0-1.65-.71l-.65.15a1.5 1.5 0 0 1-1.86-1.86l.15-.65a1.5 1.5 0 0 0-.71-1.65l-.59-.33a1.5 1.5 0 0 1 0-2.64l.59-.33a1.5 1.5 0 0 0 .71-1.65l-.15-.65a1.5 1.5 0 0 1 1.86-1.86l.65.15a1.5 1.5 0 0 0 1.65-.71l.33-.59ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>

                        <div
                            v-if="settingsMenuOpen"
                            class="absolute right-0 z-50 mt-2 w-64 border border-[#D6D6D6] bg-white p-2 shadow-lg dark:border-slate-700 dark:bg-slate-900"
                            role="menu"
                        >
                            <p class="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-[#616161] dark:text-slate-400">
                                Settings
                            </p>

                            <div class="px-3 py-2">
                                <p class="text-sm font-medium text-[#424242] dark:text-slate-200">
                                    Appearance
                                </p>
                                <p class="mt-0.5 text-xs text-[#616161] dark:text-slate-400">
                                    Choose your display theme
                                </p>

                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <button
                                        type="button"
                                        class="border px-3 py-2 text-sm font-medium transition"
                                        :class="darkMode
                                            ? 'border-[#D6D6D6] text-[#616161] hover:bg-[#EEEEEE] dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800'
                                            : 'border-[#0078D4] bg-[#DCEEFF] text-[#005A9E] dark:border-indigo-500 dark:bg-indigo-950 dark:text-indigo-300'"
                                        @click="darkMode = false; applyTheme()"
                                    >
                                        Light
                                    </button>

                                    <button
                                        type="button"
                                        class="border px-3 py-2 text-sm font-medium transition"
                                        :class="darkMode
                                            ? 'border-indigo-600 bg-indigo-50 text-indigo-700 dark:border-indigo-500 dark:bg-indigo-950 dark:text-indigo-300'
                                            : 'border-[#D6D6D6] text-[#616161] hover:bg-[#EEEEEE] dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800'"
                                        @click="darkMode = true; applyTheme()"
                                    >
                                        Dark
                                    </button>
                                </div>
                            </div>

                            <div class="my-2 border-t border-[#D6D6D6] dark:border-slate-800" />

                            <Link
                                href="/profile"
                                class="block px-3 py-2 text-sm font-medium text-[#424242] transition hover:bg-[#EEEEEE] dark:text-slate-200 dark:hover:bg-slate-800"
                                role="menuitem"
                                @click="settingsMenuOpen = false"
                            >
                                My Profile & Settings
                            </Link>
                        </div>
                    </div>

                    <!-- User profile menu -->
                    <div class="relative">
                        <button
                            type="button"
                            class="flex items-center gap-2 rounded-sm p-1.5 transition hover:bg-[#E5E5E5] focus:outline-none focus:ring-2 focus:ring-[#0078D4] dark:hover:bg-slate-800"
                            :aria-expanded="profileMenuOpen"
                            aria-haspopup="menu"
                            aria-label="Open profile menu"
                            @click="profileMenuOpen = !profileMenuOpen; settingsMenuOpen = false"
                        >
                            <span class="hidden max-w-48 text-right sm:block">
                                <span class="block truncate text-sm font-medium text-[#424242] dark:text-slate-200">
                                    {{ page.props.auth?.user?.name ?? 'Administrator' }}
                                </span>
                                <span class="block truncate text-xs text-[#616161] dark:text-slate-400">
                                    {{ page.props.auth?.user?.email ?? 'Admin account' }}
                                </span>
                            </span>

                            <img
                                v-if="page.props.auth?.user?.profile_photo_url"
                                :src="page.props.auth.user.profile_photo_url"
                                :alt="`${page.props.auth.user.name}'s profile picture`"
                                class="h-9 w-9 rounded-full object-cover"
                            >
                            <span
                                v-else
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-[#DCEEFF] text-sm font-semibold text-[#005A9E] dark:bg-indigo-950 dark:text-indigo-300"
                            >
                                {{ userInitial() }}
                            </span>

                            <svg viewBox="0 0 20 20" fill="currentColor" class="hidden h-4 w-4 text-[#777777] sm:block" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div
                            v-if="profileMenuOpen"
                            class="absolute right-0 z-50 mt-2 w-60 overflow-hidden border border-[#D6D6D6] bg-white py-2 shadow-lg dark:border-slate-700 dark:bg-slate-900"
                            role="menu"
                        >
                            <Link
                                href="/profile"
                                class="block border-b border-[#D6D6D6] px-4 py-3 transition hover:bg-[#F5F5F5] dark:border-slate-800 dark:hover:bg-slate-800"
                                role="menuitem"
                                @click="profileMenuOpen = false"
                            >
                                <p class="truncate text-sm font-semibold text-[#242424] dark:text-white">
                                    {{ page.props.auth?.user?.name ?? 'Administrator' }}
                                </p>
                                <p class="truncate text-xs text-[#616161] dark:text-slate-400">
                                    {{ page.props.auth?.user?.email ?? '' }}
                                </p>
                            </Link>

                            <button
                                type="button"
                                class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40"
                                role="menuitem"
                                @click="logout"
                            >
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                    <path
                                        fill-rule="evenodd"
                                        d="M3 4.75A1.75 1.75 0 0 1 4.75 3h5.5A1.75 1.75 0 0 1 12 4.75v1.5a.75.75 0 0 1-1.5 0v-1.5a.25.25 0 0 0-.25-.25h-5.5a.25.25 0 0 0-.25.25v10.5c0 .138.112.25.25.25h5.5a.25.25 0 0 0 .25-.25v-1.5a.75.75 0 0 1 1.5 0v1.5A1.75 1.75 0 0 1 10.25 17h-5.5A1.75 1.75 0 0 1 3 15.25V4.75Zm10.22 2.72a.75.75 0 0 1 1.06 0l2.5 2.5a.75.75 0 0 1 0 1.06l-2.5 2.5a.75.75 0 1 1-1.06-1.06l1.22-1.22H8.75a.75.75 0 0 1 0-1.5h5.69l-1.22-1.22a.75.75 0 0 1 0-1.06Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                Sign out
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6 lg:p-1">
                <slot />
            </main>
        </div>
    </div>
</template>