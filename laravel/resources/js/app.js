import "./bootstrap";
import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia"; // Import createPinia
import App from "./pages/App.vue";

const pinia = createPinia(); // Create the Pinia instance
const app = createApp(App); // Create the Vue app instance

app.use(pinia); // Tell the app to use Pinia
app.mount("#app"); // Mount the app to the #app element in your Blade template
