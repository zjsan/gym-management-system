import { defineStore } from 'pinia';
import api from '../api/api';
import { ref } from 'vue';

export const useAuthStore = defineStore('alerts', {
  // other options...
  state: () => {
    user = ref(null) || JSON.parse(localStorage.getItem("user")) || null, // stores logged-in user object
    loading = false
    error = null
  },

    /**
     * 
     * If auth.user is null → not authenticated.
       If auth.user has data → authenticated.
     */
  getters:{
    isAuthenticated: (state) => !!state.user,
  },

  actions: {

     // Save user in localStorage
    saveUserToStorage() {
        localStorage.setItem("user", JSON.stringify(this.user)); 
    },
  
    //login 
    async login(credentials) {
        this.loading = true
        this.error = null
         /* API call & set user */
         try {
            
            //get csrf cookie
            api.get('/sanctum/csrf-cookie');//intialize csrf protection

            //make the login call to the backend
            const response = await api.post('/login', credentials);

            //fetch user data after successful login to update store state
            await this.fetchUser();

            //persist data to localstorage
            this.saveUserToStorage()
            
            
         } catch (error) {
            this.error = err.response?.data?.message || "Login failed";
         }
         finally{
            this.loading = false
         }
     },

  },

})