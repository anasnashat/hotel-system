<!-- Viewing the Waiting Approval Page and then it redirects to home -->
<template>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md text-center w-full max-w-md">
            <CheckCircleIcon class="w-16 h-16 text-green-500 mx-auto mb-4" />
            <h2 class="text-2xl font-bold text-gray-800">Registration Successful!</h2>
            <p class="mt-2 text-gray-600">
                Your registration is under review. You will receive an email once your account is approved.
            </p>
            <Button class="mt-4" as-child>
                <Link href="/">Go to Home</Link>
            </Button>        
        </div>
    </div>
</template>

<script setup>
import { CheckCircleIcon } from "lucide-vue-next";
import { Button } from "@/components/ui/button";
import { Link } from '@inertiajs/vue3';
</script>
