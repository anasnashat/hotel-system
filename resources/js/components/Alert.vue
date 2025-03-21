<template>
    <div id="alert-container" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3">
        <!-- Success Alert -->
        <div
            v-if="page.props.success"
            role="alert"
            class="w-96 animate-fade-in rounded-lg border-l-4 border-l-green-500 bg-white p-4 shadow-md"
        >
            <div class="flex items-center gap-3">
                <span class="text-green-500">
                    <svg
                        class="size-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                <div class="flex-1">
                    <p class="font-medium text-green-700">{{ page.props.success }}</p>
                </div>
                <button @click="dismissSuccess" class="text-gray-400 hover:text-gray-600">
                    <span class="sr-only">Dismiss</span>
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Error Alert -->
        <div
            v-if="page.props.error"
            role="alert"
            class="w-96 animate-fade-in rounded-lg border-l-4 border-l-red-500 bg-white p-4 shadow-md"
        >
            <div class="flex items-center gap-3">
                <span class="text-red-500">
                    <svg
                        class="size-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </span>
                <div class="flex-1">
                    <p class="font-medium text-red-700">{{ page.props.error }}</p>
                </div>
                <button @click="dismissError" class="text-gray-400 hover:text-gray-600">
                    <span class="sr-only">Dismiss</span>
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Validation Errors -->
        <div
            v-if="Object.keys(page.props.errors).length > 0"
            role="alert"
            class="w-96 animate-fade-in rounded-lg border-l-4 border-l-red-500 bg-white p-4 shadow-md"
        >
            <div class="flex items-center gap-3">
                <span class="text-red-500">
                    <svg
                        class="size-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </span>
                <div class="flex-1">
                    <p class="font-medium text-red-700">Validation Errors</p>
                    <ul class="text-sm text-gray-600">
                        <li v-for="(error, field) in page.props.errors" :key="field">
                            {{ error }}
                        </li>
                    </ul>
                </div>
                <button @click="dismissValidationErrors" class="text-gray-400 hover:text-gray-600">
                    <span class="sr-only">Dismiss</span>
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';

const page = usePage();

// Dismiss success message
const dismissSuccess = () => {
    page.props.success = null;
};

// Dismiss error message
const dismissError = () => {
    page.props.error = null;
};

// Dismiss validation errors
const dismissValidationErrors = () => {
    page.props.errors = {};
};

// Auto-dismiss messages after 5 seconds
watch(
    () => page.props.success,
    (success) => {
        if (success) {
            setTimeout(() => dismissSuccess(), 5000);
        }
    },
);

watch(
    () => page.props.error,
    (error) => {
        if (error) {
            setTimeout(() => dismissError(), 5000);
        }
    },
);

watch(
    () => page.props.errors,
    (errors) => {
        if (Object.keys(errors).length > 0) {
            setTimeout(() => dismissValidationErrors(), 5000);
        }
    },
    { deep: true },
);
</script>

<style>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(20px);
    }
}
</style>
