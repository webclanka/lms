<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'

interface Props {
    token: string
    email: string
}

const props = defineProps<Props>()

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
        },
    })
}
</script>

<template>
    <AuthLayout
        title="Reset password"
        description="Please enter your new password below."
    >
        <Head title="Reset password" />

        <form @submit.prevent="submit">
            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Label
                        for="email"
                        class="text-sm font-medium text-[#424242] dark:text-slate-200"
                    >
                        Email
                    </Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        readonly
                        class="h-10 w-full rounded-sm border-[#D6D6D6] bg-[#F5F5F5] text-[#616161] dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label
                        for="password"
                        class="text-sm font-medium text-[#424242] dark:text-slate-200"
                    >
                        Password
                    </Label>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        autofocus
                        placeholder="Password"
                        class="h-10 w-full rounded-sm border-[#8A8886] bg-white text-[#242424] placeholder:text-[#616161] focus-visible:border-[#0078D4] focus-visible:ring-1 focus-visible:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label
                        for="password_confirmation"
                        class="text-sm font-medium text-[#424242] dark:text-slate-200"
                    >
                        Confirm password
                    </Label>
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        placeholder="Confirm password"
                        class="h-10 w-full rounded-sm border-[#8A8886] bg-white text-[#242424] placeholder:text-[#616161] focus-visible:border-[#0078D4] focus-visible:ring-1 focus-visible:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-1 h-10 w-full rounded-sm bg-[#0078D4] text-sm font-semibold text-white hover:bg-[#106EBE] focus-visible:ring-[#0078D4]"
                    :disabled="form.processing"
                >
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin"
                        aria-hidden="true"
                    />
                    {{ form.processing ? 'Resetting...' : 'Reset password' }}
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>