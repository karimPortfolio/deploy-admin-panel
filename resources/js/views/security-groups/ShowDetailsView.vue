<template>
    <show-modal title="Security Group Details" :loading="loading"> >
        <template #content>
            <q-card class="shadow-none bg-transparent">
                <q-card-section class="p-0 flex justify-center items-center">
                    <div
                        class="bg-primary-50 flex justify-center items-center w-fit p-3 rounded-full"
                    >
                        <q-icon name="sym_r_security" size="xl" color="primary" />
                    </div>
                </q-card-section>
                <q-card-section class="q-pa-md">
                    <div class="text-h6 text-center mb-4">
                        {{ securityGroup?.group_id }}
                    </div>
                    <!-- ======= NAME ======= -->
                    <div class="grid grid-cols-2 justify-between mt-5">
                        <div class="text-subtitle2">Name</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ securityGroup?.name }}
                        </div>
                    </div>
                    <!-- ======= VPC ID ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">VPC ID</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ securityGroup?.vpc_id || "N/A" }}
                        </div>
                    </div>
                    <!-- ======= CREATION DATE ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">Creation Date</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ securityGroup?.created_at }}
                        </div>
                    </div>
                    <!-- ======= DESCRIPTION ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">Description</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ securityGroup?.description || "N/A" }}
                        </div>
                    </div>
                    
                    <!-- ======= ASSIGNED SERVERS ======= -->
                    <template v-if="servers && servers.length" >
                        <q-separator class="mt-2" />
                        <div class="mt-5">Assigned Servers</div>
                        <div
                            class="grid grid-cols-2 gap-x-3 justify-between items-center"
                        >
                            <q-card
                                v-for="server in servers"
                                :key="server.id"
                                class="w-full flex flex-nowrap gap-2 items-center p-3 border mt-3"
                            >
                                <q-card-section class="p-0">
                                    <div
                                        class="p-2 w-fit rounded-md bg-primary-50"
                                    >
                                        <q-icon
                                            name="sym_r_host"
                                            size="sm"
                                            class="text-primary"
                                        />
                                    </div>
                                </q-card-section>
                                <q-card-section class="p-0">
                                    <div class="text-sm font-medium">
                                        {{ server.instance_id }}
                                    </div>
                                    <div class="w-fit">
                                        <q-badge
                                            :text-color="server.status.color"
                                            size="xs"
                                            class="q-mt-xs"
                                        >
                                            {{ server.status.label }}
                                        </q-badge>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                    </template>
                </q-card-section>
            </q-card>
        </template>
    </show-modal>
</template>
<script setup>
import { computed, watch } from "vue";
import ShowModal from "../../components/modals/ShowModal.vue";
import { useResourceShow } from "@/composables/useResourceShow";
import { useTextTruncate } from "@/composables/useTextTruncate";

const props = defineProps({
    id: {
        type: [String, Number],
    },
});

const {
    data: securityGroup,
    fetch,
    loading,
} = useResourceShow("security-groups");

const { truncate } = useTextTruncate();

const servers = computed(() => {
    if (!securityGroup.value.servers) return [];
    return securityGroup.value.servers.map((server) => ({
        id: server.id,
        instance_id: truncate(server.instance_id, 25),
        status: {
            label: server.status.label,
            color: server.status.color,
        },
    }));
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
