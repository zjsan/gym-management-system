<script setup>
import { ref, watch } from "vue";
import { usePaymentStore } from "@/stores/paymentStore";

const props = defineProps({
    isOpen: Boolean,
});

const emit = defineEmits(["close"]);

const paymentStore = usePaymentStore();
const period = ref("today");
const recipientEmails = ref([""]);
const successMessage = ref("");
const errorMessage = ref("");

watch(
    () => props.isOpen,
    (newVal) => {
        if (newVal) {
            successMessage.value = "";
            errorMessage.value = "";
        }
    },
);

const addEmailField = () => {
    recipientEmails.value.push("");
};

const removeEmailField = (index) => {
    recipientEmails.value.splice(index, 1);
};

const sendEmailSummary = async () => {
    successMessage.value = "";
    errorMessage.value = "";

    const payload = {
        emails: recipientEmails.value.filter((email) => email.trim() !== ""),
        period: period.value,
    };

    const result = await paymentStore.emailSalesSummary(payload);

    if (result.success) {
        successMessage.value = result.message;
        setTimeout(() => {
            emit("close");
        }, 1500);
    } else {
        errorMessage.value = result.message;
    }
};
</script>
