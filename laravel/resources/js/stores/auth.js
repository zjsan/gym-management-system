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
            

            api.get('/sanctum/csrf-cookie');//intialize csrf protection

            await api.post('/login',credentials)
            
         } catch (error) {
            
         }
     },

  },

})