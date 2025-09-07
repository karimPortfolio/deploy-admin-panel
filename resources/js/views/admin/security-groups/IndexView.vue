<template>
    <q-page class="q-pa-md">
        <!-- ======== INCLUDES ======= -->

        <show-details-view
            v-model:open="openShowDetailsModal"
            :id="itemToShow?.id"
        />

        <confirmation-modal
            v-model:open="openDeleteConfirmationModal"
            title="Delete Security Group"
            :message="`Are you sure you want to delete this security group: ${itemToDelete?.group_id} ?`"
            icon="warning"
            color="negative"
            @confirm="handleDelete"
            @cancel="openDeleteConfirmationModal = false"
        />

        <!-- ======= MAIN CONTENT ======== -->
        <page-header
            title="Security Groups"
            subtitle="Manage your security groups"
            icon="security"
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
                :rows="securityGroupsData ?? []"
                :loading="loading"
                v-model:pagination="options.pagination"
                @request="onRequest"
                flat
                :rows-per-page-options="[5, 10, 20, 30, 40, 50, 100]"
            >
                <template v-slot:body-cell-servers_count="props">
                    <q-td :props="props">
                        {{ props.row.servers_count }}
                    </q-td>
                </template>
                <template v-slot:body-cell-group_id="props">
                    <q-td :props="props" :title="props.row.group_id" >
                        {{ truncate(props.row.group_id, 20) }}
                    </q-td>
                </template>
                <template v-slot:body-cell-vpc_id="props">
                    <q-td :props="props" :title="props.row.vpc_id" >
                        {{ truncate(props.row.vpc_id, 20) }}
                    </q-td>
                </template>
                <template v-slot:body-cell-created_at="props">
                    <q-td :props="props" :title="props.row.created_at" >
                        {{ truncate(props.row.created_at, 20) }}
                        <small class="block" >By: <span class="font-medium">{{ props.row.created_by?.name || 'N/A' }}</span></small>
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <actions-column
                            :row="props.row"
                            @delete="handleDeleteConfirmation"
                            @show-details="handleShowDetails"
                        />
                    </q-td>
                </template>
            </q-table>
        </div>
    </q-page>
</template>
<script setup>
import PageHeader from "@/components/PageHeader.vue";
import { onMounted, ref } from "vue";
import { useResourceIndex } from "@/composables/useResourceIndex";
import { useResourceDestroy } from "@/composables/useResourceDestroy";
import { useTextTruncate } from "@/composables/useTextTruncate";
import FilterPanel from "@/components/FilterPanel.vue";
import SearchBar from "@/components/SearchBar.vue";
import ActionsColumn from "./table-columns/ActionsColumn.vue";
import ConfirmationModal from "@/components/modals/ConfirmationModal.vue";
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
        name: "group_id",
        label: "Group ID",
        field: "group_id",
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
        name: "vpc_id",
        label: "VPC ID",
        field: "vpc_id",
        align: "left",
        sortable: true,
    },
    {
        name: "servers_count",
        label: "Assigned Servers",
        field: "servers_count",
        align: "center",
        sortable: true,
    },
    {
        name: "created_at",
        label: "Creation Details",
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
        name: "created_at",
        label: "Creation Date",
        type: "date",
    },
];

const search = ref("");
const openCreationModal = ref(false);
const openDeleteConfirmationModal = ref(false);
const openShowDetailsModal = ref(false);
const itemToDelete = ref(null);
const itemToShow = ref(null);

const {
    data: securityGroupsData,
    loading,
    fetch,
    options,
    onRequest,
} = useResourceIndex("admin/security-groups");

const { destroy, destroyed, destroying } =
    useResourceDestroy("admin/security-groups");

const { truncate } = useTextTruncate();

const searchChange = () => {
    onRequest({
        filter: search.value,
    });
};

function onFiltersUpdate({ search, filters }) {
    fetch({ filter: search, filters });
}

const handleShowDetails = (row) => {
    itemToShow.value = row;

    openShowDetailsModal.value = true;
};

// DELETE HANDLERS
const handleDeleteConfirmation = (row) => {
    openDeleteConfirmationModal.value = true;

    itemToDelete.value = row;
};

const handleDelete = async () => {
    await destroy(itemToDelete.value.id);

    if (destroyed.value) {
        handleDeleted();
    }
};


const handleDeleted = () => {
    openDeleteConfirmationModal.value = false;

    fetch({
        filter: search.value,
        filters: options.filters,
    });
};
</script>
