<template>
    <q-page class="q-pa-md">
        <!-- ========= INCLUDES ========= -->
        <create-form
            v-model:open="openCreateServerModal"
            @created="handleCreated"
        />

        <show-details-view
            v-model:open="openShowServerModal"
            :id="itemToShow?.id"
        />

        <confirmation-modal
            v-model:open="openDeleteConfirmationModal"
            title="Delete Server"
            :message="`Are you sure you want to delete this server: ${itemToDelete?.name} ?`"
            icon="warning"
            color="negative"
            :loading="destroying"
            @confirm="handleDelete"
            @cancel="openDeleteConfirmationModal = false"
        />

        <confirmation-modal
            v-model:open="openStatusChangeConfirmationModal"
            :title="serverStatusChangeModalTitle"
            :message="`Are you sure you want to ${newServerStatus} the server: ${itemToChangeStatus?.name} ?`"
            icon="info"
            color="warning"
            :actionLabel="serverStatusChangeActionLabel"
            :loading="serverStatusChangeLoading"
            @confirm="handleStatusChange"
            @cancel="openStatusChangeConfirmationModal = false"
        />

        <!-- ======= PAGE CONTENT ======== -->
        <page-header
            title="Servers"
            subtitle="Manage your servers"
            icon="sym_r_host"
            actionLabel="Create Server"
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
                    <q-td :props="props" :title="props.row.created_by?.name ?? props.row.create_at"> 
                        {{ props.row.created_at }}
                        <div>
                            <span
                                class="font-medium me-1 text-gray-600 dark:text-gray-400"
                                >By:</span
                            >
                            <span v-if="props.row.created_by">{{
                                truncate(props.row.created_by?.name, 10)
                            }}</span>
                            <span v-else>N/A</span>
                        </div>
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <actions-column
                            :row="props.row"
                            @status-change="statusChangeConfirmation"
                            @show-details="handleShowDetails"
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
import { useTextTruncate } from "@/composables/useTextTruncate";
import SearchBar from "@/components/SearchBar.vue";
import FilterPanel from "@/components/FilterPanel.vue";
import ConfirmationModal from "@/components/modals/ConfirmationModal.vue";
import ActionsColumn from "./table-columns/ActionsColumn.vue";
import { computed, onMounted, ref } from "vue";
import CreateForm from "./CreateForm.vue";
import InstanceTypeColumn from "./table-columns/InstanceTypeColumn.vue";
import SecurityGroupColumn from "./table-columns/SecurityGroupColumn.vue";
import InstanceNameColumn from "./table-columns/InstanceNameColumn.vue";
import ShowDetailsView from "./ShowDetailsView.vue";

const columns = [
    {
        name: "id",
        label: "ID",
        field: "id",
        align: "left",
        sortable: true,
    },
    {
        name: "name",
        label: "Name",
        field: "name",
        align: "left",
        sortable: true,
    },
    {
        name: "security_group",
        label: "Security Group",
        field: "security_group",
        align: "left",
        sortable: false,
    },
    {
        name: "instance_type",
        label: "Instance Type",
        field: "instance_type",
        align: "left",
        sortable: false,
    },
    {
        name: "public_ip_address",
        label: "Public IP Address",
        field: "public_ip_address",
        align: "left",
        sortable: true,
    },
    {
        name: "status",
        label: "Status",
        field: "status",
        align: "left",
        sortable: false,
    },
    {
        name: "created_at",
        label: "Creation Info",
        field: "created_at",
        align: "left",
        sortable: true,
    },
    { label: "Actions", name: "actions", field: "actions", align: "center" },
];

const filters = [
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
        label: "Group ID",
        type: "relation",
        resource: "security-groups",
        optionLabel: "group_id",
        optionValue: "group_id",
    },
    {
        name: "instance_type",
        label: "Instance Type",
        type: "enum",
        resource: "servers/instance-types",
        optionLabel: "value",
        optionValue: "value",
    },
    {
        name: "os_family",
        label: "Operating System",
        type: "enum",
        resource: "servers/os-families",
        optionLabel: "value",
        optionValue: "value",
    },
    {
        name: "status",
        label: "Status",
        type: "enum",
        resource: "servers/statuses",
        optionLabel: "label",
        optionValue: "value",
    },
    {
        name: "created_at",
        label: "Creation Date",
        type: "date",
    },
];

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

const { truncate } = useTextTruncate();

const openCreateServerModal = ref(false);
const openShowServerModal = ref(false);
const openDeleteConfirmationModal = ref(false);
const openStatusChangeConfirmationModal = ref(false);
const itemToDelete = ref(null);
const itemToShow = ref(null);
const itemToChangeStatus = ref(null);
const newServerStatus = ref(false);

const serverStatusChangeModalTitle = computed(() => {
    return newServerStatus.value === "start" ? "Start Server" : "Stop Server";
});

const serverStatusChangeActionLabel = computed(() => {
    return newServerStatus.value === "start" ? "Start" : "Stop";
});

const serverStatusChangeLoading = computed(() => {
    return newServerStatus.value === "start" ? updating : stopping;
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

const handleStatusChange = async () => {
    if (newServerStatus.value === "start") {
        await startServer();
        openStatusChangeConfirmationModal.value = false;
        newServerStatus.value = null;
        fetch({
            filter: search.value,
            filters: options.filters,
        });
        return;
    }

    await stopServer();
    openStatusChangeConfirmationModal.value = false;
    newServerStatus.value = null;
    fetch({
        filter: search.value,
        filters: options.filters,
    });
};

const handleShowDetails = (row) => {
    itemToShow.value = row;

    openShowServerModal.value = true;
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
    // setInterval(() => {
    //     fetch({
    //         filter: search.value,
    //         filters: options.filters,
    //     });
    // }, 10000);
});
</script>
