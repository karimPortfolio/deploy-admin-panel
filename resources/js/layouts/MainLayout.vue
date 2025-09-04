<template>
    <q-layout
        view="lHh Lpr lFf"
        container
        class="h-screen overflow-hidden bg-gray-100 dark:bg-slate-900"
    >
        <!-- ========== HEADER =========== -->
        <Header :drawer="drawer" @toggleDrawer="handleDrawerToggling" />

        <!-- ========== DRAWER =========== -->
        <q-drawer
            show-if-above
            v-model="drawer"
            :width="290"
            :breakpoint="600"
            class="bg-white dark:bg-slate-800 shadow-md dark:shadow-slate-600 flex flex-col"
        >
            <div class="p-3 ps-4 pb-2">
                <a href="/">
                    <div
                        class="flex flex-nowrap items-center gap-3 text-center md:text-start"
                    >
                        <q-avatar size="lg">
                            <img
                                src="https://cdn.quasar.dev/logo-v2/svg/logo.svg"
                            />
                        </q-avatar>
                        <span class="gt-lg inline text-2xl text-bold">
                            {{ appName }}
                        </span>
                    </div>
                </a>
            </div>

            <q-list padding class="px-2 mt-4">
                <link-item
                    v-for="item in activeDrawerItems"
                    :key="item.label"
                    :item="item"
                />
            </q-list>

            <!-- ========== PROFILE DROPDOWN =========== -->
            <profile-dropdown class="mt-auto" :user="user" />
        </q-drawer>

        <!-- ========== PAGE CONTAINER =========== -->
        <q-page-container>
            <router-view />
        </q-page-container>
    </q-layout>
</template>
<script setup>
import { computed, onMounted, ref } from "vue";
import { useAuthStore } from "../stores/auth";
import ProfileDropdown from "./partials/ProfileDropdown.vue";
import { useQuasar } from "quasar";
import Header from "./partials/Header.vue";
import LinkItem from "../components/LinkItem.vue";

const drawer = ref(true);

const handleDrawerToggling = () => {
    drawer.value = !drawer.value;
};

const $q = useQuasar();

const userDrawerItems = [
    {
        label: "Dashboard",
        icon: "sym_r_dashboard",
        route: { name: "dashboard" },
        exact: true,
    },
    {
        label: "Servers",
        icon: "sym_r_host",
        route: { name: "servers.index" },
    },
    {
        label: "Security Groups",
        icon: "sym_r_security",
        route: { name: "security-groups" },
    },
    {
        label: "SSH Keys",
        icon: "sym_r_key",
        route: { name: "ssh-keys" },
    },
];

const adminDrawerItems = [
     {
        label: "Dashboard",
        icon: "sym_r_dashboard",
        route: { name: "admin.dashboard" },
        exact: true,
    },
    {
        label: "Users",
        icon: "sym_r_group",
        route: { name: "admin.users" },
    },
    {
        label: "Servers",
        icon: "sym_r_host",
        route: { name: "admin.servers.index" },
    },
    {
        label: "Security Groups",
        icon: "sym_r_security",
        route: { name: "admin.security-groups" },
    },
    {
        label: "SSH Keys",
        icon: "sym_r_key",
        route: { name: "admin.ssh-keys" },
    },
];

const authStore = useAuthStore();

const user = ref(null);
const appName = ref("");

const activeDrawerItems = computed(() => {
    if (user.value && user.value.role?.value === "admin") {
        return adminDrawerItems;
    }

    return userDrawerItems;
});

const userPreferences = computed(() => {
    if (user.value && user.value.preferences) {
        return user.value.preferences;
    }

    return [];
});

const setDarkModePreference = () => {
    if (userPreferences.value && userPreferences.value.length) {
        const userThemePreference =
            userPreferences.value[0]?.preferences?.theme ?? "auto";
        localStorage.setItem("dark", userThemePreference);
        return;
    }

    const savedTheme = localStorage.getItem("dark") ?? "auto";
    localStorage.setItem("dark", savedTheme);
};

const handleDarkMode = () => {
    const userThemePreference = localStorage.getItem("dark");
    if (userThemePreference === "auto") {
        $q.dark.set(userThemePreference);
        return;
    }
    
    const darkMode = JSON.parse(userThemePreference) ?? "auto";
    $q.dark.set(darkMode);
};

onMounted(() => {
    user.value = authStore.user;
    appName.value = import.meta.env.VITE_APP_NAME; //load from env
    setDarkModePreference();
    handleDarkMode();
});
</script>
<style scoped>
:deep(.q-field--outlined .q-field__control) {
    padding-right: 8px !important;
    border-radius: 8px !important;
}
</style>
