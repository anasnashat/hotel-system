<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login');
};
</script>

<template>
    <div class="flex h-screen items-center justify-center">
        <form @submit.prevent="submit" class="w-96 rounded-lg bg-white p-6 shadow-lg">
            <h2 class="mb-4 text-xl font-bold">Login</h2>

            <label class="mb-2 block">Email</label>
            <input v-model="form.email" type="email" class="mb-4 w-full rounded border p-2" />
            <div v-if="form.errors.email" class="text-red-500">{{ form.errors.email }}</div>

            <label class="mb-2 block">Password</label>
            <input v-model="form.password" type="password" class="mb-4 w-full rounded border p-2" />
            <div v-if="form.errors.password" class="text-red-500">{{ form.errors.password }}</div>

            <div class="flex items-center">
                <input v-model="form.remember" type="checkbox" class="mr-2" />
                <label>Remember Me</label>
            </div>

            <button type="submit" class="mt-4 w-full rounded bg-blue-500 px-4 py-2 text-white" :disabled="form.processing">Login</button>
        </form>
    </div>
</template>
