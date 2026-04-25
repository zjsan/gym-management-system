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
        <div class="p-6">
            <h2 class="text-xl font-bold mb-4">User Management</h2>
            
            <button
        class="ml-3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        :disabled="userStore.loading"
    >
        {{ isEditing ? 'Update User' : 'Save User' }}
    </button>
    <button v-if="isEditing" @click="resetForm" type="button" class="ml-2 text-gray-500 underline">
        Cancel
    </button>

    <table class="min-w-full bg-white border mt-5">
        <thead>
            <tr>
                <th class="border px-4 py-2">Name</th>
                <th class="border px-4 py-2">Email</th>
                <th class="border px-4 py-2">Role</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="user in userStore.users" :key="user.id">
                <td class="border px-4 py-2">{{ user.first_name }} {{ user.last_name }}</td>
                <td class="border px-4 py-2">{{ user.email }}</td>
                <td class="border px-4 py-2">
                    <span class="px-2 py-1 bg-gray-200 rounded text-sm">{{ user.role?.slug }}</span>
                </td>
                <td class="border px-4 py-2 text-center">
                    <button @click="editUser(user)" class="text-blue-600 mr-3">Edit</button>
                    <button @click="confirmDelete(user.id)" class="text-red-600">Delete</button>
                </td>
            </tr>
        </tbody>
    </table>
            
        </div>
        <div v-if="userStore.loading" class="mt-4 text-center">Loading users...</div>
    </template>

<script setup>
import { ref } from "vue";
import { useUserStore } from "@/stores/userStore";
import { onMounted } from 'vue';

const userStore = useUserStore();
const initialState = {
    first_name: "",
    last_name: "",
    email: "",
    role: "staff",
    password: "",
    password_confirmation: "",
};


const isEditing = ref(false);
const currentUserId = ref(null);

const editUser = (user) => {
    isEditing.value = true;
    currentUserId.value = user.id;
    
    // Fill the form with existing data
    form.value = {
        first_name: user.first_name,
        last_name: user.last_name,
        email: user.email,
        role: user.role?.slug,
        password: "", // Leave blank for security
        password_confirmation: "",
    };
};

const form = ref({ ...initialState });

const handleSubmit = async () => {
    try {
        let result;
        
        if(isEditing.value){
            result = await userStore.updateUser(currentUserId.value, form.value)
        }
        else{
                const result = await userStore.addUser(form.value);
        }
        if (result && result.success) {
            resetForm();
            alert(isEditing.value ? "User updated!" : "User added!");
        }
    } catch (error) {
        console.error("Failed operation:", error);
        alert("Failed to operation. Please check the console for details.");
    }
};

const confirmDelete = async (id) => {
    if (confirm("Are you sure you want to delete this user?")) {
        await userStore.deleteUser(id);
    }
};

const resetForm = () => {
    isEditing.value = false;
    currentUserId.value = null;
    form.value = { ...initialState };
};

onMounted(() => {
    userStore.fetchUsers();
});
</script>
