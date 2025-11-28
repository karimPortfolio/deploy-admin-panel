<template>
    <show-modal title="roles.role_details" :loading="loading">
        <template #content>
            <q-card class="shadow-none bg-transparent">
                <q-card-section class="p-0 flex justify-center items-center">
                    <div
                        class="bg-primary-50 flex justify-center items-center w-fit p-3 rounded-full"
                    >
                        <q-icon name="sym_r_admin_panel_settings" size="xl" color="primary" />
                    </div>
                </q-card-section>
                <q-card-section class="q-pa-md">
                    <div class="text-h6 text-center mb-4">
                        {{ role?.name }}
                    </div>
                    <!-- ======= CREATION DATE ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">{{ $t("creation_details") }}</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ role?.created_at }}
                            <div class="text-sm" v-if="role?.created_by">
                                {{ $t("by") }}:
                                <span class="text-primary">{{
                                    role?.created_by?.name
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- ======= PERMISSIONS ======= -->
                    <template v-if="permissions && permissions.length">
                        <q-separator class="mt-2" />
                        <div class="mt-5">{{ $t("permissions") }}</div>
                        <div class="grid grid-cols-2 gap-2 mt-3">
                            <q-chip
                                v-for="permission in permissions"
                                :key="permission.id"
                                color="primary"
                                text-color="white"
                                size="sm"
                            >
                                {{ permission.name }}
                            </q-chip>
                        </div>
                    </template>
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

const { data: role, fetch, loading } = useResourceShow("admin/roles");

const permissions = computed(() => {
    if (!role.value.permissions) return [];
    return role.value.permissions;
});

watch(
    () => props.id,
    (newId) => {
        if (newId) {
            fetch(newId);
        }
    }
);
</script>
