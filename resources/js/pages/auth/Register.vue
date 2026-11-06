<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AuthBase from '@/layouts/AuthLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account."
    >
        <Head title="Register" />

        <form class="flex flex-col gap-6" @submit.prevent="submit">
            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Label
                        for="name"
                        class="text-sm font-medium text-[#424242] dark:text-slate-200"
                    >
                        Name
                    </Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="name"
                        placeholder="Full name"
                        class="h-10 rounded-sm border-[#8A8886] bg-white text-[#242424] placeholder:text-[#616161] focus-visible:border-[#0078D4] focus-visible:ring-1 focus-visible:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                    />
                    <InputError :message="form.errors.name" />
                </div>

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
                        tabindex="2"
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="h-10 rounded-sm border-[#8A8886] bg-white text-[#242424] placeholder:text-[#616161] focus-visible:border-[#0078D4] focus-visible:ring-1 focus-visible:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
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
                        required
                        tabindex="3"
                        autocomplete="new-password"
                        placeholder="Password"
                        class="h-10 rounded-sm border-[#8A8886] bg-white text-[#242424] placeholder:text-[#616161] focus-visible:border-[#0078D4] focus-visible:ring-1 focus-visible:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
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
                        required
                        tabindex="4"
                        autocomplete="new-password"
                        placeholder="Confirm password"
                        class="h-10 rounded-sm border-[#8A8886] bg-white text-[#242424] placeholder:text-[#616161] focus-visible:border-[#0078D4] focus-visible:ring-1 focus-visible:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-1 h-10 w-full rounded-sm bg-[#0078D4] text-sm font-semibold text-white hover:bg-[#106EBE] focus-visible:ring-[#0078D4]"
                    tabindex="5"
                    :disabled="form.processing"
                >
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin"
                        aria-hidden="true"
                    />
                    {{ form.processing ? 'Creating account...' : 'Create account' }}
                </Button>
            </div>

            <div class="border-t border-[#D6D6D6] pt-5 text-center text-sm text-[#616161] dark:border-slate-800 dark:text-slate-400">
                Already have an account?
                <TextLink
                    :href="route('login')"
                    class="font-medium text-[#005A9E] hover:underline dark:text-sky-300"
                    :tabindex="6"
                >
                    Log in
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>