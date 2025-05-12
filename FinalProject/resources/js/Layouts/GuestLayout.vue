<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="guestContentContainer"
        >
        <div>
            <Link href="/">
                <ApplicationLogo class="h-30 w-30 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="guestContent"
        >
            <slot />
        </div>
    </div>
</template>
