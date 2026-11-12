<script setup>
defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    secondsRemaining: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(["extend"]);
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
    >
        <div
            class="w-full max-w-md p-6 bg-white rounded-xl shadow-2xl border border-gray-100 text-center space-y-4"
        >
            <div
                class="w-12 h-12 mx-auto bg-amber-100 text-amber-600 rounded-full flex items-center justify-center text-xl font-bold"
            >
                ⚠️
            </div>
            <h3 class="text-xl font-semibold text-gray-900">
                Are you still there?
            </h3>
            <p class="text-sm text-gray-600">
                Your terminal has been inactive. For security, you will be
                automatically logged out in
                <span class="font-bold text-red-600"
                    >{{ secondsRemaining }} seconds</span
                >.
            </p>
            <div class="pt-2">
                <button
                    @click="emit('extend')"
                    class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition shadow-sm"
                >
                    Yes, Keep Working
                </button>
            </div>
        </div>
    </div>
</template>
