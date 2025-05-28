<template>
    <q-header
        :class="{
            'bg-gray-50 text-gray-700': !$q.dark.isActive,
            'bg-dark text-gray-300': $q.dark.isActive,
        }"
        class="shadow-sm"
    >
        <q-toolbar>
            <q-btn
                dense
                flat
                size="sm"
                padding="sm"
                icon="menu"
                @click="handleDrawerToggling"
            />
            <!-- ============== SEARCH BAR =============== -->
            <div class="ms-5 w-1/3 py-3">
                <q-input
                    dense
                    placeholder="Search..."
                    outlined
                    ref="searchInput"
                >
                    <template v-slot:prepend>
                        <q-icon name="sym_r_search" size="xs" />
                    </template>
                </q-input>
            </div>
            <!-- ============== SEARCH BAR =============== -->

            <q-space />

            <!-- ============== NOTIFICATIONS =============== -->
            <div class="mr-2">
                <q-btn dense size="sm" padding="sm" flat>
                    <span class="relative">
                        <q-icon name="sym_r_notifications" />
                        <span
                            class="absolute top-0 right-0 w-2 h-2 dark:bg-primary-300 bg-primary ring-2 ring-white dark:ring-slate-800 rounded-full"
                        ></span>
                    </span>
                </q-btn>
            </div>
            <!-- ============== NOTIFICATIONS =============== -->

            <div>
                <q-btn
                    dense
                    flat
                    size="sm"
                    padding="sm"
                    icon="dark_mode"
                    @click="handleDarkToggling"
                />
            </div>
        </q-toolbar>
    </q-header>
</template>
<script setup>
import { useQuasar } from "quasar";

const $q = useQuasar();

const emits = defineEmits(["toggleDrawer"]);

const props = defineProps({
    drawer: {
        type: Boolean,
    },
});

const handleDarkToggling = () => {
    $q.dark.toggle();
    localStorage.setItem("dark", $q.dark.isActive);
};

const handleDrawerToggling = () => {
    emits("toggleDrawer");
};

</script>
