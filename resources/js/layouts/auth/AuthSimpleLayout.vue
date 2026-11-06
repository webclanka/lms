<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { Link } from '@inertiajs/vue3'

defineProps<{
    title?: string
    description?: string
}>()
</script>

<template>
    <main class="flex min-h-svh flex-col items-center justify-center bg-[#EBEBEB] p-4 text-[#242424] dark:bg-slate-950 dark:text-slate-100 sm:p-6">
        <div class="w-full max-w-md overflow-hidden border border-[#D6D6D6] bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <header class="flex flex-col items-center gap-4 border-b border-[#D6D6D6] px-6 py-7 dark:border-slate-800">
                <Link
                    :href="route('home')"
                    class="flex items-center justify-center rounded-sm focus:outline-none focus:ring-2 focus:ring-[#0078D4] focus:ring-offset-2"
                    aria-label="Go to home"
                >
                <span class="flex h-11 w-11 items-center justify-center">
    <img
        src="/logo.png"
        alt="Sasinna LMS"
        class="h-11 w-11 object-contain"
    >
</span>
                </Link>

                <div class="space-y-1 text-center">
                    <h1 class="text-xl font-semibold text-[#242424] dark:text-white">
                        {{ title }}
                    </h1>
                    <p class="text-sm leading-5 text-[#616161] dark:text-slate-400">
                        {{ description }}
                    </p>
                </div>
            </header>

            <div class="p-6 sm:p-7">
                <slot />
            </div>
        </div>

        <p class="mt-5 text-center text-xs text-[#616161] dark:text-slate-500">
            Sasinna LMS
        </p>
    </main>
</template>