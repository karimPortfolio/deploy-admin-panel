<template>
    <q-page class="q-pa-md">
        <!-- ========= INCLUDES ========= -->
        <create-form
            v-model:open="openCreateServerModal"
            @created="handleCreated"
        />

        <!-- <show-details-view
            v-model:open="openShowServerModal"
            :id="itemToShow?.id"
        /> -->

        <confirmation-modal
            v-model:open="openDeleteConfirmationModal"
            title="servers.delete"
            icon="warning"
            color="negative"
            :loading="destroying"
            @confirm="handleDelete"
            @cancel="openDeleteConfirmationModal = false"
        />

        <confirmation-modal
            v-model:open="openStatusChangeConfirmationModal"
            :title="serverStatusChangeModalTitle"
            icon="info"
            color="warning"
            :actionLabel="serverStatusChangeActionLabel"
            :loading="serverStatusChangeLoading"
            @confirm="handleStatusChange"
            @cancel="openStatusChangeConfirmationModal = false"
        />

        <!-- ======= PAGE CONTENT ======== -->
        <page-header
            title="servers.title"
            subtitle="servers.subtitle"
            icon="sym_r_host"
            actionLabel="servers.create"
            actionIcon="add"
            :action="handleCreate"
        />

        <div class="mt-4 bg-white dark:bg-slate-800 p-3 rounded-md">
            <div class="flex items-center justify-between gap-3 mb-3">
                <!-- ========= SEARCH BAR ======== -->
                <search-bar
                    @search="searchChange"
                    v-model:searchValue="search"
                    class="md:max-w-sm max-w-full"
                />

                <!-- ======== FILTERS BAR ========-->
                <filter-panel :filters="filters" @update="onFiltersUpdate" />
            </div>

            <!-- ======== TABLE ========= -->
            <q-table
                :columns="columns"
                :rows="data ?? []"
                :loading="loading"
                v-model:pagination="options.pagination"
                @request="onRequest"
                flat
                :rows-per-page-options="[5, 10, 20, 30, 40, 50, 100]"
            >
                <template v-slot:body-cell-name="props">
                    <q-td :props="props" :title="props.row.instance_id">
                        <instance-name-column :row="props.row" />
                    </q-td>
                </template>
                <template v-slot:body-cell-security_group="props">
                    <q-td
                        :props="props"
                        :title="props.row.security_group.group_id"
                    >
                        <security-group-column :row="props.row" />
                    </q-td>
                </template>
                <template v-slot:body-cell-instance_type="props">
                    <q-td :props="props" :title="props.row.image_id">
                        <instance-type-column :row="props.row" />
                    </q-td>
                </template>
                <template v-slot:body-cell-public_ip_address="props">
                    <q-td :props="props" :title="props.row.public_ip_address">
                        {{ props.row.public_ip_address ?? "N/A" }}
                    </q-td>
                </template>
                <template v-slot:body-cell-status="props">
                    <q-td :props="props" :title="props.row.status.label">
                        <q-badge
                            :text-color="props.row.status.color"
                            size="xs"
                            class="w-fit"
                        >
                            {{ props.row.status.label }}
                        </q-badge>
                    </q-td>
                </template>
                <template v-slot:body-cell-created_at="props">
                    <q-td
                        :props="props"
                        :title="
                            props.row.created_by?.name ?? props.row.create_at
                        "
                    >
                        {{ props.row.created_at }}
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <actions-column
                            :row="props.row"
                            @status-change="statusChangeConfirmation"
                            @delete="handleDeleteConfirmation"
                        />
                    </q-td>
                </template>
            </q-table>
        </div>
    </q-page>
</template>
<script setup>
import PageHeader from "@/components/PageHeader.vue";
import { useResourceIndex } from "@/composables/useResourceIndex";
import { useResourceDestroy } from "@/composables/useResourceDestroy";
import { useResourceUpdate } from "@/composables/useResourceUpdate";
import { usePollingInterval } from "@/composables/usePollingInterval";
import SearchBar from "@/components/SearchBar.vue";
import FilterPanel from "@/components/FilterPanel.vue";
import ConfirmationModal from "@/components/modals/ConfirmationModal.vue";
import ActionsColumn from "./table-columns/ActionsColumn.vue";
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import CreateForm from "./CreateForm.vue";
import InstanceTypeColumn from "./table-columns/InstanceTypeColumn.vue";
import SecurityGroupColumn from "./table-columns/SecurityGroupColumn.vue";
import InstanceNameColumn from "./table-columns/InstanceNameColumn.vue";
import { useI18n } from "vue-i18n";
import { useDebounceFn } from "@vueuse/core";

const { t } = useI18n();

const columns = computed(() => [
    {
        name: "id",
        label: t("id"),
        field: "id",
        align: "left",
        sortable: true,
    },
    {
        name: "name",
        label: t("name"),
        field: "name",
        align: "left",
        sortable: true,
    },
    {
        name: "security_group",
        label: t("security_groups.title"),
        field: "security_group",
        align: "left",
        sortable: false,
    },
    {
        name: "instance_type",
        label: t("servers.instance_type"),
        field: "instance_type",
        align: "left",
        sortable: false,
    },
    {
        name: "public_ip_address",
        label: t("servers.public_ip_address"),
        field: "public_ip_address",
        align: "left",
        sortable: true,
    },
    {
        name: "status",
        label: t("status"),
        field: "status",
        align: "left",
        sortable: false,
    },
    {
        name: "created_at",
        label: t("creation_date"),
        field: "created_at",
        align: "left",
        sortable: true,
    },
    { label: t("actions"), name: "actions", field: "actions", align: "center" },
]);

const filters = computed(() => [
    {
        name: "vpc_id",
        label: "VPC ID",
        type: "relation",
        resource: "vpcs",
        optionLabel: "vpc_id",
        optionValue: "vpc_id",
    },
    {
        name: "security_group_id",
        label: t("security_groups.group_id"),
        type: "relation",
        resource: "security-groups",
        optionLabel: "group_id",
        optionValue: "group_id",
    },
    {
        name: "instance_type",
        label: t("servers.instance_type"),
        type: "enum",
        resource: "servers/instance-types",
        optionLabel: "value",
        optionValue: "value",
    },
    {
        name: "os_family",
        label: t("servers.operating_system"),
        type: "enum",
        resource: "servers/os-families",
        optionLabel: "value",
        optionValue: "value",
    },
    {
        name: "status",
        label: t("status"),
        type: "enum",
        resource: "servers/statuses",
        optionLabel: "label",
        optionValue: "value",
    },
    {
        name: "created_at",
        label: t("creation_date"),
        type: "date",
    },
]);

const search = ref("");

const { data, fetch, loading, options, onRequest } =
    useResourceIndex("servers");

const { destroy, destroying, destroyed } = useResourceDestroy("servers");

const { update: startServer, updating } = useResourceUpdate(
    () => `servers/${itemToChangeStatus.value.id}/start`
);

const { update: stopServer, updating: stopping } = useResourceUpdate(
    () => `servers/${itemToChangeStatus.value.id}/stop`
);

const { start, stop } = usePollingInterval(()  => {
    fetch({
        filter: search.value,
        filters: options.filters,
    });
}, 20000);

const openCreateServerModal = ref(false);
const openDeleteConfirmationModal = ref(false);
const openStatusChangeConfirmationModal = ref(false);
const itemToDelete = ref(null);
const itemToChangeStatus = ref(null);
const newServerStatus = ref(false);

const serverStatusChangeModalTitle = computed(() => {
    return newServerStatus.value === "start"
        ? t("servers.start_server")
        : t("servers.stop_server");
});

const serverStatusChangeActionLabel = computed(() => {
    return newServerStatus.value === "start"
        ? t("servers.start")
        : t("servers.stop");
});

const serverStatusChangeLoading = computed(() => {
    return newServerStatus.value === "start" ? updating.value : stopping.value;
});

const searchChange = () => {
    onRequest({
        filter: search.value,
    });
};

function onFiltersUpdate({ search, filters }) {
    fetch({ filter: search, filters });
}

const handleCreate = () => {
    openCreateServerModal.value = true;
};

const handleDeleteConfirmation = (row) => {
    itemToDelete.value = row;

    openDeleteConfirmationModal.value = true;
};

const statusChangeConfirmation = (row, status) => {
    itemToChangeStatus.value = row;
    newServerStatus.value = status;

    openStatusChangeConfirmationModal.value = true;
};

const statusChanged = () => {
    openStatusChangeConfirmationModal.value = false;
    newServerStatus.value = null;
    fetch({
        filter: search.value,
        filters: options.filters,
    });
};

const handleStatusChange = async () => {
    if (newServerStatus.value === "start") {
        await startServer();
        statusChanged();
        return;
    }

    await stopServer();
    statusChanged();
};


const handleDelete = async () => {
    await destroy(itemToDelete.value.id);

    if (destroyed.value) {
        openDeleteConfirmationModal.value = false;
        fetch({
            filter: search.value,
            filters: options.filters,
        });
    }
};

const handleCreated = () => {
    openCreateServerModal.value = false;

    fetch({
        filter: search.value,
        filters: options.filters,
    });
};

onMounted(() => {
    useDebounceFn(start, 3000)();
});

onBeforeUnmount(() => {
    stop();
});
</script>
