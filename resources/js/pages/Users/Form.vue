<script setup>
import { onBeforeUnmount, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
    roles: {
        type: Array,
        required: true,
    },
})

const showPassword = ref(false)
const photoPreview = ref(props.user?.profile_photo_url ?? null)
let temporaryPhotoUrl = null

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
    role: props.user?.role ?? 'student',
    profile_photo: null,
})

function selectPhoto(event) {
    const file = event.target.files?.[0] ?? null
    form.profile_photo = file

    if (temporaryPhotoUrl) {
        URL.revokeObjectURL(temporaryPhotoUrl)
        temporaryPhotoUrl = null
    }

    if (file) {
        temporaryPhotoUrl = URL.createObjectURL(file)
        photoPreview.value = temporaryPhotoUrl
    } else {
        photoPreview.value = props.user?.profile_photo_url ?? null
    }
}

function submit() {
    const options = {
        onSuccess: () => form.reset('password'),
    }

    if (props.user) {
        // POST with method spoofing supports multipart file uploads in Laravel.
        form.transform((data) => ({
            ...data,
            _method: 'put',
        })).post(`/admin/users/${props.user.id}`, options)
    } else {
        form.post('/admin/users', options)
    }
}

function roleLabel(role) {
    return role.charAt(0).toUpperCase() + role.slice(1)
}

onBeforeUnmount(() => {
    if (temporaryPhotoUrl) {
        URL.revokeObjectURL(temporaryPhotoUrl)
    }
})
</script>

<template>
    <Head :title="user ? 'Edit user' : 'Create user'" />

    <div class="mx-auto w-full max-w-3xl text-[#242424] dark:text-slate-100">
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
            <header class="border-b border-[#D6D6D6] px-5 py-5 dark:border-slate-800 sm:px-7">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-sm bg-[#DCEEFF] text-[#005A9E] dark:bg-indigo-950 dark:text-indigo-300">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                            <path d="M10 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8ZM3 16a7 7 0 0 1 14 0v1H3v-1Z" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-[#005A9E] dark:text-sky-300">
                            User management
                        </p>
                        <h1 class="mt-1 text-xl font-semibold text-[#242424] dark:text-white">
                            {{ user ? 'Edit user' : 'Create user' }}
                        </h1>
                        <p class="mt-1 text-sm text-[#616161] dark:text-slate-400">
                            {{ user
                                ? 'Update this account’s details and access role.'
                                : 'Add an account and choose its access role.' }}
                        </p>
                    </div>
                </div>
            </header>

            <form class="space-y-5 p-5 sm:p-7" @submit.prevent="submit">
                <div
                    v-if="Object.keys(form.errors).length"
                    class="border-l-4 border-rose-600 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-400 dark:bg-rose-950/40 dark:text-rose-300"
                    role="alert"
                >
                    Please review the highlighted fields and try again.
                </div>

                <div>
                    <label for="profile_photo" class="mb-1.5 block text-sm font-semibold text-[#424242] dark:text-slate-300">
                        Profile picture
                    </label>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <img
                            v-if="photoPreview"
                            :src="photoPreview"
                            alt="Profile picture preview"
                            class="h-16 w-16 shrink-0 rounded-full object-cover ring-1 ring-[#D6D6D6] dark:ring-slate-700"
                        >
                        <div
                            v-else
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-[#DCEEFF] text-xl font-semibold text-[#005A9E] dark:bg-indigo-950 dark:text-indigo-300"
                            aria-hidden="true"
                        >
                            {{ form.name.charAt(0).toUpperCase() || '?' }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <input
                                id="profile_photo"
                                type="file"
                                accept="image/*"
                                class="block w-full text-sm text-[#616161] file:mr-3 file:rounded-sm file:border-0 file:bg-[#E8E8E8] file:px-3 file:py-2 file:font-semibold file:text-[#424242] hover:file:bg-[#DEDEDE] dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-slate-200 dark:hover:file:bg-slate-700"
                                :aria-invalid="Boolean(form.errors.profile_photo)"
                                aria-describedby="profile-photo-help profile-photo-error"
                                @change="selectPhoto"
                            >
                            <p id="profile-photo-help" class="mt-1.5 text-xs text-[#616161] dark:text-slate-400">
                                Select an image up to 2 MB.
                            </p>
                            <p
                                v-if="form.errors.profile_photo"
                                id="profile-photo-error"
                                class="mt-1.5 text-sm text-rose-600 dark:text-rose-400"
                            >
                                {{ form.errors.profile_photo }}
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="name" class="mb-1.5 block text-sm font-semibold text-[#424242] dark:text-slate-300">
                        Full name
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        autocomplete="name"
                        required
                        maxlength="255"
                        :aria-invalid="Boolean(form.errors.name)"
                        :aria-describedby="form.errors.name ? 'name-error' : undefined"
                        class="w-full rounded-sm border border-[#8A8886] bg-white px-3 py-2.5 text-sm text-[#242424] outline-none transition placeholder:text-[#616161] focus:border-[#0078D4] focus:ring-1 focus:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                        placeholder="e.g. Alex Morgan"
                    />
                    <p v-if="form.errors.name" id="name-error" class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">
                        {{ form.errors.name }}
                    </p>
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-[#424242] dark:text-slate-300">
                        Email address
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        required
                        maxlength="255"
                        :aria-invalid="Boolean(form.errors.email)"
                        :aria-describedby="form.errors.email ? 'email-error' : undefined"
                        class="w-full rounded-sm border border-[#8A8886] bg-white px-3 py-2.5 text-sm text-[#242424] outline-none transition placeholder:text-[#616161] focus:border-[#0078D4] focus:ring-1 focus:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                        placeholder="name@example.com"
                    />
                    <p v-if="form.errors.email" id="email-error" class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">
                        {{ form.errors.email }}
                    </p>
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-[#424242] dark:text-slate-300">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            :required="!user"
                            minlength="8"
                            autocomplete="new-password"
                            :aria-invalid="Boolean(form.errors.password)"
                            :aria-describedby="form.errors.password ? 'password-error' : 'password-help'"
                            class="w-full rounded-sm border border-[#8A8886] bg-white px-3 py-2.5 pr-16 text-sm text-[#242424] outline-none transition placeholder:text-[#616161] focus:border-[#0078D4] focus:ring-1 focus:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                            :placeholder="user ? 'Leave blank to keep current password' : 'At least 8 characters'"
                        >
                        <button
                            type="button"
                            class="absolute inset-y-0 right-3 text-sm font-medium text-[#005A9E] hover:underline dark:text-sky-300"
                            :aria-label="showPassword ? 'Hide password' : 'Show password'"
                            @click="showPassword = !showPassword"
                        >
                            {{ showPassword ? 'Hide' : 'Show' }}
                        </button>
                    </div>
                    <p
                        v-if="!form.errors.password"
                        id="password-help"
                        class="mt-1.5 text-xs text-[#616161] dark:text-slate-400"
                    >
                        {{ user
                            ? 'Leave this blank if you do not want to change the password.'
                            : 'Use at least 8 characters.' }}
                    </p>
                    <p v-if="form.errors.password" id="password-error" class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">
                        {{ form.errors.password }}
                    </p>
                </div>

                <div>
                    <label for="role" class="mb-1.5 block text-sm font-semibold text-[#424242] dark:text-slate-300">
                        Account role
                    </label>
                    <select
                        id="role"
                        v-model="form.role"
                        required
                        :aria-invalid="Boolean(form.errors.role)"
                        :aria-describedby="form.errors.role ? 'role-error' : 'role-help'"
                        class="w-full rounded-sm border border-[#8A8886] bg-white px-3 py-2.5 text-sm text-[#242424] outline-none transition focus:border-[#0078D4] focus:ring-1 focus:ring-[#0078D4] dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                    >
                        <option v-for="role in roles" :key="role" :value="role">
                            {{ roleLabel(role) }}
                        </option>
                    </select>
                    <p
                        v-if="!form.errors.role"
                        id="role-help"
                        class="mt-1.5 text-xs text-[#616161] dark:text-slate-400"
                    >
                        The role determines which areas of the LMS this account can access.
                    </p>
                    <p v-if="form.errors.role" id="role-error" class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">
                        {{ form.errors.role }}
                    </p>
                </div>

                <p v-if="form.errors.user" class="text-sm text-rose-600 dark:text-rose-400" role="alert">
                    {{ form.errors.user }}
                </p>

                <div class="flex flex-col-reverse gap-3 border-t border-[#D6D6D6] pt-5 dark:border-slate-800 sm:flex-row sm:justify-end">
                    <Link
                        href="/admin/users"
                        class="inline-flex justify-center rounded-sm border border-[#8A8886] bg-white px-4 py-2.5 text-sm font-semibold text-[#424242] transition hover:bg-[#F5F5F5] focus:outline-none focus:ring-2 focus:ring-[#0078D4] focus:ring-offset-2 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-900"
                    >
                        Cancel
                    </Link>

                    <button
                        type="submit"
                        class="inline-flex justify-center rounded-sm bg-[#0078D4] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#106EBE] focus:outline-none focus:ring-2 focus:ring-[#0078D4] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:focus:ring-offset-slate-900"
                        :disabled="form.processing"
                    >
                        {{ form.processing
                            ? 'Saving...'
                            : user
                                ? 'Save changes'
                                : 'Create user' }}
                    </button>
                </div>
            </form>
        </section>
    </div>
</template>