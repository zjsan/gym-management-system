<template>
    <div class="container">
        <h1>Staff Dashboard</h1>
        <p>
            Welcome to the Staff dashboard! This page is protected and requires
            authentication and only for staff.
        </p>
    </div>
</template>
<script setup>
import { computed, ref } from "vue";
import { useMemberStore } from "@/stores/memberStore";
import { onMounted } from "vue";
</script>
