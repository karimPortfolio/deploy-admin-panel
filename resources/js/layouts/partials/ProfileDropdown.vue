<template>
    <div class="mb-2 p-2">
        <q-card
            class="dark:bg-slate-700 overflow-hidden bg-gray-50 shadow-none flex flex-nowrap items-center gap-4 p-2 py-3 w-full"
        >
            <q-card-section class="p-0">
                <q-avatar size="lg">
                    <q-img
                        :src="
                            user?.avatar ??
                            'https://cdn.quasar.dev/img/boy-avatar.png'
                        "
                    />
                </q-avatar>
            </q-card-section>
            <q-card-section class="p-0 truncate">
                <div class="text-sm font-medium">
                    {{ user?.name }}
                </div>
                <div class="text-xs dark:text-gray-400 text-grey-8 truncate">
                    {{ user?.email }}
                </div>
            </q-card-section>

            <!-- ========== DROPDOWN =========== -->
            <q-card-section class="p-0 ms-auto me-2">
                <q-btn
                    icon="sym_r_unfold_more"
                    padding="sm"
                    size="sm"
                    unelevated
                >
                    <q-menu
                        :offset="[0, 6]"
                        class="q-pa-xs rounded-lg dark:bg-slate-700 shadow-md border dark:border-0 w-fit"
                    >
                        <!-- ========== USER INFO =========== -->
                        <div class="flex flex-nowrap p-2 px-4 gap-3">
                            <!-- ==== AVATAR === -->
                            <div>
                                <q-avatar size="lg">
                                    <q-img
                                        :src="
                                            user?.avatar ??
                                            'https://cdn.quasar.dev/img/boy-avatar.png'
                                        "
                                    />
                                </q-avatar>
                            </div>
                            <!-- ==== AVATAR === -->
                            <!-- ==== INFO === -->
                            <div class="overflow-hidden" >
                                <div class="flex items-center gap-3">
                                    <div class="text-sm font-medium">
                                        {{ user?.name }}
                                    </div>
                                    <div v-if="user?.role" >
                                        <q-badge
                                            :color="user?.role.color"
                                            :label="user?.role.label"
                                            class="text-2xs"
                                        />
                                    </div>
                                </div>
                                <div class="text-xs dark:text-gray-400 text-grey-8">
                                    {{ user?.email }}                        
                                </div>
                            </div>
                            <!-- ==== INFO === -->
                        </div>
                        <!-- ========== USER INFO =========== -->

                        <q-separator inset />

                        <q-list style="min-width: 150px" dense class="q-pa-xs mt-1">
                            <q-item
                                target="_blank"
                                v-close-popup
                                class="py-2 hover:cursor-pointer rounded-md hover:bg-gray-100 dark:hover:bg-gray-600"
                            >
                                <q-item-section avatar class="min-w-fit pe-2">
                                    <q-icon name="sym_r_person" size="sm" />
                                </q-item-section>

                                <q-item-section>View Profile</q-item-section>
                            </q-item>
                            <q-item
                                clickable
                                v-close-popup
                                class="py-2 mb-2 hover:cursor-pointer rounded-md hover:bg-gray-100 dark:hover:bg-gray-600"
                            >
                                <q-item-section avatar class="min-w-fit pe-2">
                                    <q-icon name="sym_r_settings" size="sm" />
                                </q-item-section>

                                <q-item-section> Settings </q-item-section>
                            </q-item>

                            <q-separator />

                            <q-item
                                clickable
                                @click="handleLogOut"
                                v-close-popup
                                class="py-2 mt-2 hover:cursor-pointer rounded-md hover:bg-gray-100 dark:hover:bg-gray-600"
                            >
                                <q-item-section avatar class="min-w-fit pe-2">
                                    <q-icon name="sym_r_logout" size="sm" />
                                </q-item-section>

                                <q-item-section> Sign out </q-item-section>
                            </q-item>
                        </q-list>
                    </q-menu>
                </q-btn>
            </q-card-section>
        </q-card>
    </div>
</template>
<script setup>
import { useAuthStore } from "../../stores/auth";

const props = defineProps({
    user: {
        type: Object,
    }
});

const authStore = useAuthStore();

async function handleLogOut() {
    await authStore.logout();
    props.user = null;
}
</script>
