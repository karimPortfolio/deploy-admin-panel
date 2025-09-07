<template>
    <div class="mb-2 p-2">
        <q-card
            class="dark:bg-slate-700 overflow-hidden bg-gray-50 shadow-none flex flex-nowrap items-center gap-4 p-2 py-3 w-full"
        >
            <q-card-section class="p-0">
                <q-avatar size="lg">
                    <q-img
                        :src="
                            user?.photo ??
                            '/src/img/avatar.png'
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
                                            user?.photo ??
                                            '/src/img/avatar.png'
                                        "
                                    />
                                </q-avatar>
                            </div>
                            <!-- ==== AVATAR === -->
                            <!-- ==== INFO === -->
                            <div class="overflow-hidden">
                                <div class="text-sm font-medium">
                                    {{ user?.name }}
                                </div>
                                <div
                                    class="text-xs dark:text-gray-400 text-grey-8"
                                >
                                    {{ user?.email }}
                                </div>
                            </div>
                            <!-- ==== INFO === -->
                        </div>

                        <q-separator class="mx-1" />

                        <profile-dropdown-list @logout="handleLogOut" />
                    </q-menu>
                </q-btn>
            </q-card-section>
        </q-card>
    </div>
</template>
<script setup>
import ProfileDropdownList from "@/components/ProfileDropdownList.vue";
import { useAuthStore } from "@/stores/auth";

const props = defineProps({
    user: {
        type: Object,
    },
});

const authStore = useAuthStore();

async function handleLogOut() {
    await authStore.logout();
    props.user = null;
}
</script>
