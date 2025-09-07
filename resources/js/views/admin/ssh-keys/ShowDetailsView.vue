<template>
    <show-modal title="SSH Key Details" :loading="loading">
        <template #content>
            <q-card class="shadow-none bg-transparent">
                <q-card-section class="p-0 flex justify-center items-center">
                    <div
                        class="bg-primary-50 flex justify-center items-center w-fit p-3 rounded-full"
                    >
                        <q-icon name="sym_r_key" size="xl" color="primary" />
                    </div>
                </q-card-section>
                <q-card-section class="q-pa-md">
                    <div class="text-h6 text-center mb-4">
                        {{ sshKey?.name }}
                    </div>
                    <!-- ======= PUBLIC KEY ======= -->
                    <div class="grid grid-cols-2 justify-between mt-5">
                        <div class="text-subtitle2">Public Key</div>
                        <div
                            class="text-gray-700 dark:text-gray-400 text-end"
                            style="
                                font-family: monospace, sans-serif;
                                word-break: break-all;
                                white-space: normal;
                            "
                        >
                            {{ sshKey?.public_key }}*****
                        </div>
                    </div>
                    <!-- ======= CREATION DATE ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">Creation Details</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ sshKey?.created_at }}
                            <div class="text-sm">
                                By
                                <span class="text-primary">{{
                                    sshKey?.created_by?.name
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- ======= ASSIGNED SERVERS ======= -->
                    <template v-if="servers && servers.length">
                        <q-separator class="mt-2" />
                        <div class="mt-5">Assigned Servers</div>
                        <div
                            class="grid grid-cols-2 justify-between items-center"
                        >
                            <template
                                v-for="server in servers"
                                :key="server.id"
                            >
                                <router-link
                                    :to="{
                                        name: 'admin.servers.show',
                                        params: { id: server.id },
                                    }"
                                >
                                    <q-card
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
                                            <div
                                                v-if="
                                                    server.instance_id &&
                                                    server.instance_id !== null
                                                "
                                                class="text-sm font-medium"
                                            >
                                                {{ server.instance_id }}
                                            </div>
                                            <div
                                                v-else
                                                class="text-sm font-medium"
                                            >
                                                N/A
                                            </div>
                                            <div class="w-fit">
                                                <q-badge
                                                    :text-color="
                                                        server.status.color
                                                    "
                                                    size="xs"
                                                    class="q-mt-xs"
                                                >
                                                    {{ server.status.label }}
                                                </q-badge>
                                            </div>
                                        </q-card-section>
                                    </q-card>
                                </router-link>
                            </template>
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
import { useTextTruncate } from "@/composables/useTextTruncate";

const props = defineProps({
    id: {
        type: [String, Number],
    },
});

const { data: sshKey, fetch, loading } = useResourceShow("admin/ssh-keys");

const { truncate } = useTextTruncate();

const servers = computed(() => {
    if (!sshKey.value.servers) return [];
    return sshKey.value.servers.map((server) => ({
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
