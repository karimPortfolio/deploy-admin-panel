<template>
    <q-card class="p-4">
        <q-card-section class="p-0 flex items-center gap-2 pb-4">
            <q-icon name="sym_r_link" color="primary" size="sm" />
            <h4 class="font-meduim text-lg">
                {{ $t("databases.attached_servers") }}
            </h4>
        </q-card-section>
        <template v-if="servers && servers.length">
            <q-card-section>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 justify-between items-center"
                >
                    <template v-for="server in servers" :key="server.id">
                        <router-link 
                            :to="{
                                name: 'admin.servers.show',
                                params: { id: server.id },
                            }">
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

                                <q-card-section class="p-0 flex-1">
                                    <!-- ====== INSTANCE ID ===== -->
                                    <div
                                        v-if="
                                            server.instance_id &&
                                            server.instance_id !== null
                                        "
                                        class="text-sm font-medium"
                                    >
                                        {{ server.instance_id }} -
                                        {{ server.name }}
                                    </div>
                                    <div v-else class="text-sm font-medium">
                                        N/A
                                    </div>

                                    <div
                                        class="flex flex-nowrap items-center justify-betweeen"
                                    >
                                        <!-- ======== STATUS & PRIMARY ===== -->
                                        <div
                                            class="flex flex-nowrap gap-2 items-center mt-1"
                                        >
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
                                            <div
                                                v-if="server.pivot?.is_primary"
                                                class="w-fit"
                                            >
                                                <q-badge
                                                    text-color="primary"
                                                    size="xs"
                                                    class="q-mt-xs"
                                                >
                                                    {{
                                                        $t("databases.primary")
                                                    }}
                                                </q-badge>
                                            </div>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </router-link>
                    </template>
                </div>
            </q-card-section>
        </template>
        <template v-else>
            <q-card-section class="pt-2 px-2">
                <warning-alert message="no_data_available_msg" />
            </q-card-section>
        </template>
        <q-card-section v-if="loading" class="p-0">
            <q-inner-loading :showing="loading" class="rounded-lg">
                <q-spinner-tail color="primary" size="40px" />
            </q-inner-loading>
        </q-card-section>
    </q-card>
</template>
<script setup>
import WarningAlert from "@/components/alerts/WarningAlert.vue";

const props = defineProps({
    servers: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>
