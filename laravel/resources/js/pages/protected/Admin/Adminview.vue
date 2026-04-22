<template>
    <h1>Admin Dashboard</h1>
    <p>
        Welcome to the Admin dashboard! This page is protected and requires
        authentication and only for admin.
    </p>

    <div>
        <form @submit.prevent="handleSubmit">
            <input v-model="form.name" type="text" placeholder="Full Name" />
            <input
                v-model="form.email"
                type="email"
                placeholder="Email Address"
            />

            <select v-model="form.role">
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
            </select>

            <button :disabled="userStore.loading">Save User</button>

            <p v-if="userStore.errors" class="text-red-500">
                {{ userStore.errors.email[0] }}
            </p>
        </form>
    </div>
</template>
<script setup></script>
