<template>
    <q-card class="p-4">
        <q-card-section class="p-0 flex items-center gap-2 pb-0">
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
                        <q-card
                            class="w-full flex flex-nowrap gap-3 items-center p-3 border mt-3"
                        >
                            <q-card-section class="p-0">
                                <div class="p-2 w-fit rounded-md bg-primary-50">
                                    <q-icon
                                        name="sym_r_host"
                                        size="sm"
                                        class="text-primary"
                                    />
                                </div>
                            </q-card-section>

                            <q-card-section class="p-0 flex-1">
                                <!-- ====== INSTANCE ID ===== -->
                                <div class="flex items-center justify-between">
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

                                    <!-- ======= ACTIONS ====== -->
                                    <actions-dropdown>
                                        <template #items>
                                            <template 
                                                v-for="(
                                                    item, index
                                                ) in itemsActions"
                                                :key="index">
                                                <q-item
                                                    :to="item.link ? item.link : null"
                                                    v-show="item.type !== 'edit' || (item.type === 'edit' && !server.pivot?.is_primary)"
                                                    clickable
                                                    class="text-gray-600 dark:text-gray-200 rounded-md text-sm"
                                                    @click="item.action(server)"
                                                >
                                                    <q-item-section>
                                                        <q-item-label
                                                            class="flex-nowrap"
                                                        >
                                                            <q-icon
                                                                :name="item.icon"
                                                                size="xs"
                                                                class="me-1"
                                                            />
                                                            {{ $t(item.label) }}
                                                        </q-item-label>
                                                    </q-item-section>
                                                </q-item>
                                            </template>
                                        </template>
                                    </actions-dropdown>
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
                                                {{ $t("databases.primary") }}
                                            </q-badge>
                                        </div>
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                    </template>
                </div>
            </q-card-section>
        </template>
        <template v-else>
            <q-card-section class="pt-3 px-2">
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
import ActionsDropdown from "../../../components/ActionsDropdown.vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

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

const emit = defineEmits(['update', 'delete']);

const router = useRouter();

const itemsActions = [
    {
        label: "view_details",
        action: (server) => {
            router.push({ name: "servers.show", params: { id: server.id } });
        },
        icon: "sym_r_visibility",
        type: "view"
    },
    {
        label: "databases.make_primary",
        action: (server) => emit('update', server.pivot, server),
        icon: "sym_r_edit",
        type: "edit"
    },
    {
        label: "databases.delete_association",
        action: (server) => emit('delete', server.pivot),
        icon: "sym_r_link_off",
        type: "delete"
    },
];

</script>
