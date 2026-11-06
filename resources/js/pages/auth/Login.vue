<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AuthBase from '@/layouts/AuthLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'

defineProps<{
    status?: string
    canResetPassword: boolean
}>()

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <AuthBase
        title="Log in to your account"
        description="Enter your email and password below to log in."
    >
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-5 border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-400 dark:bg-emerald-950/40 dark:text-emerald-300"
            role="status"
        >
            {{ status }}
        </div>

        <form class="flex flex-col gap-6" @submit.prevent="submit">
            <div class="grid gap-5">
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
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="h-10 rounded-sm border-[#8A8886] bg-white text-[#242424] placeholder:text-[#616161] focus-visible:border-[#0078D4] focus-visible:ring-1 focus-visible:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between gap-3">
                        <Label
                            for="password"
                            class="text-sm font-medium text-[#424242] dark:text-slate-200"
                        >
                            Password
                        </Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-[#005A9E] hover:underline dark:text-sky-300"
                            tabindex="5"
                        >
                            Forgot password?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        tabindex="2"
                        autocomplete="current-password"
                        placeholder="Password"
                        class="h-10 rounded-sm border-[#8A8886] bg-white text-[#242424] placeholder:text-[#616161] focus-visible:border-[#0078D4] focus-visible:ring-1 focus-visible:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center" tabindex="3">
                    <Label
                        for="remember"
                        class="flex cursor-pointer items-center gap-3 text-sm font-normal text-[#424242] dark:text-slate-300"
                    >
                        <Checkbox
                            id="remember"
                            v-model:checked="form.remember"
                            tabindex="4"
                            class="border-[#8A8886] data-[state=checked]:border-[#0078D4] data-[state=checked]:bg-[#0078D4]"
                        />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-1 h-10 w-full rounded-sm bg-[#0078D4] text-sm font-semibold text-white hover:bg-[#106EBE] focus-visible:ring-[#0078D4]"
                    tabindex="4"
                    :disabled="form.processing"
                >
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin"
                        aria-hidden="true"
                    />
                    {{ form.processing ? 'Logging in...' : 'Log in' }}
                </Button>
            </div>

            <div class="border-t border-[#D6D6D6] pt-5 text-center text-sm text-[#616161] dark:border-slate-800 dark:text-slate-400">
                Don’t have an account?
                <TextLink
                    :href="route('register')"
                    class="font-medium text-[#005A9E] hover:underline dark:text-sky-300"
                    :tabindex="5"
                >
                    Sign up
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>