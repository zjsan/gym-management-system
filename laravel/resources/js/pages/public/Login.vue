<template>
    <h1>login</h1>
    <form @submit.prevent="submit">
        <input type="email" v-model="email" placeholder="Email" />
        <input type="password" v-model="password" placeholder="Password" />
        <button type="submit">Login</button>
    </form>
</template>
