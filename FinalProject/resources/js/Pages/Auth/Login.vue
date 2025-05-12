<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <header class="flex justify-between items-center p-6 shadow-md">
            <Link href="/theArt">
                <img src="/images/navLogos.png" alt="The Art Logo" class="h-16" />
            </Link>
            <nav class="space-x-4">
                <Link href="/theArt/home" class="nav-link ml-4 text-sm">Home</Link>
                <Link href="/theArt/artCatalog" class="nav-link ml-4 text-sm">Artworks</Link>
                <Link href="/theArt/artistCatalog" class="nav-link ml-4 text-sm">Artists</Link>
                <Link v-if="auth?.user" href="/theArt/savedCollection" class="nav-link ml-4 text-sm">My Collection</Link>
                <Link v-if="auth?.user" href="/theArt/upload" class="nav-link ml-4 text-sm">AI Critique</Link>
                <Link href="/theArt/about" class="nav-link ml-4 text-sm">About</Link>
                <Link v-if="canLogin && !auth?.user" :href="route('login')" class="ml-4 text-sm">Log in</Link>
                <Link v-if="canRegister && !auth?.user" :href="route('register')" class="text-sm">Register</Link>
            </nav>
        </header>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
            <div class="mt-6 text-center text-sm text-gray-600">
            <span>New here?</span>
            <Link
                :href="route('register')"
                class="text-indigo-600 hover:text-indigo-900 underline ml-1"
            >
                Create an account
            </Link>
            </div>
        </form>
    </GuestLayout>
</template>
