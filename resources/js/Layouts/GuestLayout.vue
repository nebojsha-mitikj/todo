<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <Link href="/login">
                <ApplicationLogo class="text-3xl" />
            </Link>
        </div>
        <div
            class="w-full sm:max-w-md mt-6 sm:px-6 px-4 py-4 sm:mb-0 mb-8 sm:bg-white sm:shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
