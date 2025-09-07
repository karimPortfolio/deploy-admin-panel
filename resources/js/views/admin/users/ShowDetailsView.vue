<template>
    <show-modal title="User Details" :loading="loading">
        >
        <template #content>
            <q-card class="shadow-none bg-transparent">
                <q-card-section class="p-0 flex justify-center items-center">
                    <div
                        class="flex justify-center items-center w-fit border rounded-full"
                    >
                        <q-avatar class="w-20 h-20">
                            <q-img
                                :src="user?.photo ?? '/src/img/avatar.png'"
                            />
                        </q-avatar>
                    </div>
                </q-card-section>
                <q-card-section class="q-pa-md">
                    <div class="text-h6 text-center mb-4">
                        {{ user?.group_id }}
                    </div>
                    <!-- ======= NAME ======= -->
                    <div class="grid grid-cols-2 justify-between mt-5">
                        <div class="text-subtitle2">Name</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ user?.name }}
                        </div>
                    </div>
                    <!-- ======= COMPANY NAME ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">Company Name</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ user?.company_name || "N/A" }}
                        </div>
                    </div>

                    <!-- ======= EMAIL ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">Email</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ user?.email || "N/A" }}
                        </div>
                    </div>

                    <!-- ======= ROLE ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">Role</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ user?.role?.label || "N/A" }}
                        </div>
                    </div>

                    <!-- ======= STATUS ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">Status</div>
                        <div
                            class="text-gray-700 dark:text-gray-400 flex justify-end"
                        >
                            <q-badge
                                v-if="user?.is_active"
                                text-color="positive"
                                label="Active"
                                class="w-fit"
                            />
                            <q-badge
                                v-else
                                text-color="negative"
                                label="Inactive"
                                class="w-fit"
                            />
                        </div>
                    </div>

                    <!-- ======= NUMBER OF SERVERS ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">Total servers created</div>
                        <div class="text-gray-700 dark:text-gray-400 flex justify-end">
                            <q-badge text-color="primary" class="w-fit">
                                <q-icon
                                    name="sym_r_host"
                                    class="inline-block mr-1"
                                />
                                {{ user?.servers_count }}
                            </q-badge>
                        </div>
                    </div>

                    <!-- ======= NUMBER OF SECURITY GROUPS ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">
                            Total security groups created
                        </div>
                        <div class="text-gray-700 dark:text-gray-400 flex justify-end">
                            <q-badge text-color="primary" class="w-fit">
                                <q-icon
                                    name="sym_r_security"
                                    class="inline-block mr-1"
                                />
                                {{ user?.security_groups_count }}
                            </q-badge>
                        </div>
                    </div>

                    <!-- ======= NUMBER OF SSH KEYS ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">Total ssh keys created</div>
                        <div class="text-gray-700 dark:text-gray-400 flex justify-end">
                            <q-badge text-color="primary" class="w-fit">
                                <q-icon
                                    name="sym_r_key"
                                    class="inline-block mr-1"
                                />
                                {{ user?.ssh_keys_count }}
                            </q-badge>
                        </div>
                    </div>

                    <!-- ======= CREATION DATE ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">Creation Date</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ user?.created_at }}
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </template>
    </show-modal>
</template>
<script setup>
import { computed, watch } from "vue";
import ShowModal from "@/components/modals/ShowModal.vue";
import { useResourceShow } from "@/composables/useResourceShow";

const props = defineProps({
    id: {
        type: [String, Number],
    },
});

const { data: user, fetch, loading } = useResourceShow("admin/users");

watch(
    () => props.id,
    (newId) => {
        if (newId) {
            fetch(newId);
        }
    }
);
</script>
