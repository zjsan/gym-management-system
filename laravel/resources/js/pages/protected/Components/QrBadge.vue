<template>
    <!-- 🪪 QR BADGE MODAL -->
    <div
        v-if="showQrModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4"
    >
        <div
            class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-sm w-full p-6 text-center relative animate-in fade-in zoom-in duration-150"
        >
            <!-- Close Button -->
            <button
                @click="closeQrModal"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-sm p-1"
            >
                ✕
            </button>

            <h3 class="text-lg font-bold text-slate-800 mb-1">
                Member QR Badge
            </h3>
            <p class="text-xs text-slate-500 mb-4">
                {{ selectedQrMember?.first_name }}
                {{ selectedQrMember?.last_name }} (#{{
                    selectedQrMember?.membership_no
                }})
            </p>

            <!-- Loading State -->
            <div
                v-if="memberStore.qrLoading"
                class="py-12 text-slate-400 text-xs italic"
            >
                Generating member badge...
            </div>

            <!-- Badge Display Area (printable target) -->
            <div
                v-else-if="memberStore.qrData"
                id="printable-qr-badge"
                class="bg-slate-50 border border-slate-200 rounded-xl p-4 my-2 flex flex-col items-center"
            >
                <!-- Render SVG raw output from backend resource -->
                <div
                    v-if="memberStore.qrData.qr_code"
                    v-html="memberStore.qrData.qr_code"
                    class="w-48 h-48 flex items-center justify-center my-2"
                ></div>
                <div v-else class="text-xs text-slate-400 py-8">
                    No SVG element found in payload.
                </div>

                <div
                    class="font-mono text-xs font-bold text-slate-700 tracking-wider"
                >
                    {{ selectedQrMember?.membership_no }}
                </div>
                <div class="text-[10px] text-slate-400 uppercase mt-0.5">
                    Scan at Front Desk Scanner
                </div>
            </div>

            <!-- Action Controls -->
            <div class="flex flex-col gap-2 mt-4">
                <button
                    @click="printQrBadge"
                    :disabled="!memberStore.qrData || memberStore.qrLoading"
                    class="w-full py-2.5 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition disabled:opacity-50"
                >
                    🖨️ Print Badge
                </button>

                <button
                    @click="handleSendQrEmail"
                    :disabled="isEmailingQr"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium disabled:opacity-50"
                >
                    {{ isEmailingQr ? "Sending..." : "✉️ Email QR Code" }}
                </button>

                <button
                    @click="handleRegenerateQr"
                    :disabled="memberStore.qrLoading"
                    class="w-full py-2 px-4 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg border border-rose-200 transition disabled:opacity-50"
                >
                    🔄 Regenerate Token
                </button>
            </div>
        </div>
    </div>
</template>
<script setup></script>
