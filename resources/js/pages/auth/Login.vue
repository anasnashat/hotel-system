<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = ref({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    router.post('/login', form.value);
};
</script>

<template>
    <div class="flex h-screen items-center justify-center">
        <form @submit.prevent="submit" class="w-96 rounded-lg bg-white p-6 shadow-lg">
            <h2 class="mb-4 text-xl font-bold">Login</h2>

            <label class="mb-2 block">Email</label>
            <input v-model="form.email" type="email" class="mb-4 w-full rounded border p-2" />

            <label class="mb-2 block">Password</label>
            <input v-model="form.password" type="password" class="mb-4 w-full rounded border p-2" />

            <div class="flex items-center">
                <input v-model="form.remember" type="checkbox" class="mr-2" />
                <label>Remember Me</label>
            </div>

            <button type="submit" class="mt-4 w-full rounded bg-blue-500 px-4 py-2 text-white">Login</button>
        </form>
    </div>
</template>
