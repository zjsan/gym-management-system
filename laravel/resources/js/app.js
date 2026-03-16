import "./bootstrap";
import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia"; // Import createPinia
import App from "./components/App.vue"; // Assuming you have an App component

const pinia = createPinia(); // Create the Pinia instance
const app = createApp(App); // Create the Vue app instance

app.use(pinia); // Tell the app to use Pinia
// If you use Vue Router, you would also add:
// import router from './router';
// app.use(router);

app.mount("#app"); // Mount the app to the #app element in your Blade template
