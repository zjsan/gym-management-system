<template>
    <!-- hero section -->
    <section
        class="min-h-screen py-20 px-6 flex flex items-center justify-center items-start bg-gray-800"
    >
        <div class="mt-2 max-w-2xl">
            <h1 class="text-4xl font-bold text-white mb-6">
                IMAQ - GYM MANAGEMENT SYSTEM
            </h1>
            <p class="text-lg text-gray-300 mb-8">
                Welcome to IMAQ, the ultimate gym management system designed to
                streamline your fitness center's operations.
            </p>
            <div
                class="flex flex-col justify-center min-h-full space-y-4 bg-white py-15 px-20 rounded-lg shadow-md"
            >
                <form @submit.prevent="login">
                    <input
                        id="email"
                        type="email"
                        v-model="email"
                        placeholder="Email"
                        class="px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white border border-gray-700"
                        required
                    />
                    <input
                        id="password"
                        type="password"
                        v-model="password"
                        placeholder="Password"
                        class="px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white mt-4 border border-gray-700"
                        required
                    />
                    <button
                        type="submit"
                        class="mt-8 px-20 py-2 bg-blue-500 text-white font-bold rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Sign in
                    </button>

                    <p class="text-sm text-gray-500 mt-4">
                        Forgot Password?
                        <a href="#" class="text-blue-500 hover:underline">
                            Click here to reset
                        </a>
                    </p>

                    <p class="text-sm text-gray-500 mt-4">
                        Don't have an account?
                        <a href="#" class="text-blue-500 hover:underline">
                            Sign up
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </section>
</template> 
<script setup>
import { useAuthStore } from '../../stores/auth';
import { ref } from 'vue';

const auth = useAuthStore();
const email 

const login = () => {
    try {
        auth.login();
    } catch (error) {

        if (error.response && error.response.status === 422) {
            console.log('Validation Errors:', error.response.data.errors);
        }
    }
};//call login function from auth store
 </script>
