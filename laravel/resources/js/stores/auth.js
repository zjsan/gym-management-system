import { defineStore } from 'pinia';
import api from '../api/api';
import { ref } from 'vue';

export const useAuthStore = defineStore('alerts', {
  // other options...
  state: () => {
    user = ref(null) || JSON.parse(localStorage.getItem("user")) || null, // stores logged-in user object
    loading = false
    error = false
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

  },

})