    <template>
        <h1>Admin Dashboard</h1>
        <p>
            Welcome to the Admin dashboard! This page is protected and requires
            authentication and only for admin.
        </p>

        <div class="mb-10">
            <form @submit.prevent="handleSubmit">
                <input
                    v-model="form.first_name"
                    type="text"
                    placeholder="First Name"
                    required
                />
                <input
                    v-model="form.last_name"
                    type="text"
                    placeholder="Last Name"
                    required
                />
                <input
                    v-model="form.email"
                    type="email"
                    placeholder="Email Address"
                    required
                />
                <input
                    v-model="form.password"
                    type="password"
                    placeholder="Password"
                    required
                />
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    placeholder="Confirm Password"
                    required
                />
                <select v-model="form.role">
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>

                <button
                    class="ml-3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    :disabled="userStore.loading"
                >
                    Save User
                </button>

                <p v-if="userStore.errors?.email" class="text-red-500">
                    {{ userStore.errors.email[0] }}
                </p>
            </form>
        </div>
        <div>
            
        </div>
    </template>
    <script setup>
    import { ref } from "vue";
    import { useUserStore } from "@/stores/userStore";

    const userStore = useUserStore();
    const initialState = {
        first_name: "",
        last_name: "",
        email: "",
        role: "staff",
        password: "",
        password_confirmation: "",
    };

    const form = ref({ ...initialState });

    const handleSubmit = async () => {
        try {
            const result = await userStore.addUser(form.value);
            // Reset form or show success message

            if (result.success) {
                form.value = { ...initialState };
                alert("User added successfully!");
            }
        } catch (error) {
            console.error("Failed to add user:", error);
            alert("Failed to add user. Please check the console for details.");
        }
    };
    </script>
