<template>
    <div class="container">
        <h1>Dashboard</h1>
        
        <Adminview v-if="userRole === 'admin'" />
        <Staffview v-else-if="userRole === 'staff'" />

        <button @click="logout" class="bg-red-500 text-white font-semibold rounded-lg shadow hover:bg-red-600 transition">
            Logout
        </button>
    </div>
</template>
<script setup>
import { useAuthStore } from '../../../stores/auth';
import { ref, computed } from 'vue'
import Adminview from '../Admin/Adminview.vue';
import Staffview from '../Staff/Staffview.vue';

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

computed: {
  userRole() {
    return this.$store.state.user.role; // Example using Pinia/Vuex
  }
};
</script>
