<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'

defineProps<{
    status?: string
}>()

const form = useForm({})

const submit = () => {
    form.post(route('verification.send'))
}
</script>

<template>
    <AuthLayout
        title="Verify email"
        description="Please verify your email address by clicking the link we just emailed to you."
    >
        <Head title="Email verification" />

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-5 border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm font-medium leading-5 text-emerald-800 dark:border-emerald-400 dark:bg-emerald-950/40 dark:text-emerald-300"
            role="status"
        >
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form class="space-y-5" @submit.prevent="submit">
            <Button
                type="submit"
                class="h-10 w-full rounded-sm bg-[#0078D4] text-sm font-semibold text-white hover:bg-[#106EBE] focus-visible:ring-[#0078D4]"
                :disabled="form.processing"
            >
                <LoaderCircle
                    v-if="form.processing"
                    class="h-4 w-4 animate-spin"
                    aria-hidden="true"
                />
                {{ form.processing ? 'Sending...' : 'Resend verification email' }}
            </Button>

            <div class="border-t border-[#D6D6D6] pt-5 text-center dark:border-slate-800">
                <TextLink
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm font-medium text-[#005A9E] hover:underline dark:text-sky-300"
                >
                    Log out
                </TextLink>
            </div>
        </form>
    </AuthLayout>
</template>