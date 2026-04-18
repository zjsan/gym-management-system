<template>
    <div class="container">
        <h1>Dashboard</h1>
        <p>
            Welcome to the dashboard! This page is protected and requires
            authentication.
        </p>

        <button @click="logout" class="bg-red-500 text-white font-semibold rounded-lg shadow hover:bg-red-600 transition">
            Logout
        </button>
    </div>
</template>
<script setup>
import { useAuthStore } from '../../../stores/auth';

const auth = useAuthStore();
const logout = async () => {
    try {
        console.log('Attempting to logout...')
        await auth.logout();
        console.log('Success! Redirecting...');
    } catch (error) {
        console.error('Logout failed:', error);
    }
    
};
</script>
