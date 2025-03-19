<template>
    <div>
        <h1>Admin Dashboard</h1>
        <p>Welcome, Admin!</p>
        <h2>Managers</h2>
        <ul>
            <li v-for="manager in managers" :key="manager.id">{{ manager.name }} ({{ manager.email }})</li>
        </ul>
    </div>
</template>

<script setup>
defineProps({ managers: Array });
</script>
