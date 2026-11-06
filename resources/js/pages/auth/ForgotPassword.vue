<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'

defineProps<{
    status?: string
}>()

const form = useForm({
    email: '',
})

const submit = () => {
    form.post(route('password.email'))
}
</script>

<template>
    <AuthLayout
        title="Forgot password"
        description="Enter your email to receive a password reset link."
    >
        <Head title="Forgot password" />

        <div
            v-if="status"
            class="mb-5 border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-400 dark:bg-emerald-950/40 dark:text-emerald-300"
            role="status"
        >
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label
                        for="email"
                        class="text-sm font-medium text-[#424242] dark:text-slate-200"
                    >
                        Email address
                    </Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        autofocus
                        placeholder="email@example.com"
                        class="h-10 rounded-sm border-[#8A8886] bg-white text-[#242424] placeholder:text-[#616161] focus-visible:border-[#0078D4] focus-visible:ring-1 focus-visible:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="mt-6">
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
                        {{ form.processing ? 'Sending...' : 'Email password reset link' }}
                    </Button>
                </div>
            </form>

            <div class="border-t border-[#D6D6D6] pt-5 text-center text-sm text-[#616161] dark:border-slate-800 dark:text-slate-400">
                <span>Or, return to </span>
                <TextLink
                    :href="route('login')"
                    class="font-medium text-[#005A9E] hover:underline dark:text-sky-300"
                >
                    log in
                </TextLink>
            </div>
        </div>
    </AuthLayout>
</template>