import { createMemoryHistory, createRouter } from 'vue-router';

import Login from "./views/Login.vue";
import Main from "./views/Main.vue";

const routes = [
    { path: "/login", component: Login },
    { path: "/main/:username/:password", component: Main }
]

const router = createRouter({
    history: createMemoryHistory(),
    routes
})

export default router