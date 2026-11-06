<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'

const form = useForm({
    password: '',
})

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset()
        },
    })
}
</script>

<template>
    <AuthLayout
        title="Confirm your password"
        description="This is a secure area of the application. Please confirm your password before continuing."
    >
        <Head title="Confirm password" />

        <form @submit.prevent="submit">
            <div class="space-y-6">
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
                        autocomplete="current-password"
                        autofocus
                        class="h-10 w-full rounded-sm border-[#8A8886] bg-white text-[#242424] placeholder:text-[#616161] focus-visible:border-[#0078D4] focus-visible:ring-1 focus-visible:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                    />

                    <InputError :message="form.errors.password" />
                </div>

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
                    {{ form.processing ? 'Confirming...' : 'Confirm password' }}
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>