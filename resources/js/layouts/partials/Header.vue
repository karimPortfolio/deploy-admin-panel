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
                icon="sym_r_menu"
                @click="handleDrawerToggling"
            >
                <q-tooltip
                    v-if="$q.screen.gt.sm"
                    anchor="center end"
                    self="center middle"
                    :offset="[63, 10]"
                >
                    <span v-if="drawer">{{ $t("close_sidebar") }}</span>
                    <span v-else>{{ $t("open_sidebar") }}</span>
                </q-tooltip>
            </q-btn>
            <!-- ============== SEARCH BAR =============== -->
            <!-- <div class="ms-5 w-1/3 py-3">
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
            </div> -->
            <!-- ============== SEARCH BAR =============== -->

            <q-space />

            <!-- ============== NOTIFICATIONS =============== -->
            <div class="mr-2">
                <notifications />
            </div>
            <!-- ============== NOTIFICATIONS =============== -->

            <div>
                <q-btn
                    dense
                    flat
                    size="sm"
                    padding="sm"
                    :icon="
                        $q.dark.isActive
                            ? 'sym_r_light_mode'
                            : 'sym_r_dark_mode'
                    "
                    @click="handleDarkToggling"
                />
            </div>
        </q-toolbar>
    </q-header>
</template>
<script setup>
import { useQuasar } from "quasar";
import Notifications from "./Notifications.vue";

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
