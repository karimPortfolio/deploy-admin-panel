<template>
    <q-page class="q-pa-md">

        <!-- ======= INCLUDES ====== -->
         <create-form
            v-model:open="openCreateModal"
            @created="handleCreated"
        />

        <edit-form
            v-model:open="openEditModal"
            :id="itemToUpdate?.id"
            @updated="handleUpdated"
        />

        <show-details-view
            v-model:open="openShowModal"
            :id="itemToShow?.id"
        />

        <confirmation-modal
            v-model:open="openDeleteConfirmationModal"
            title="Delete SSH Key"
            :message="`Are you sure you want to delete this SSH key: ${itemToDelete?.name} ?`"
            icon="warning"
            color="negative"
            :loading="destroying"
            @confirm="handleDelete"
            @cancel="openDeleteConfirmationModal = false"
        />

        <!-- ===== PAGE CONTENT === -->
        <page-header
            title="SSH Keys"
            subtitle="Manage your SSH keys"
            icon="key"
            actionLabel="Create SSH Key"
            actionIcon="add"
            :action="() => openCreateModal=true"
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
                :rows="sshKeysData ?? []"
                :loading="loading"
                v-model:pagination="options.pagination"
                @request="onRequest"
                flat
                :rows-per-page-options="[5, 10, 20, 30, 40, 50, 100]"
            >
                <template v-slot:body-cell-public_key="props">
                    <q-td :props="props">
                        <span class="break-all" style="font-family: monospace, sans-serif;" >{{ props.row.public_key }}*****</span>
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <actions-column
                            :row="props.row"
                            @show-details="handleShow"
                            @edit="handleEdit"
                            @delete="handleConfirmDelete"
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
import FilterPanel from "@/components/FilterPanel.vue";
import SearchBar from "@/components/SearchBar.vue";
import { ref } from "vue";
import ActionsColumn from "./table-columns/ActionsColumn.vue";
import CreateForm from "./CreateForm.vue";
import EditForm from "./EditForm.vue";
import ConfirmationModal from "@/components/modals/ConfirmationModal.vue";
import ShowDetailsView from "./ShowDetailsView.vue";

const {
    data: sshKeysData,
    fetch,
    loading,
    options,
    onRequest
} = useResourceIndex('ssh-keys');

const { destroy, destroyed, destroying } =
    useResourceDestroy("ssh-keys");

const columns = [
    { name: 'id', label: 'ID', field: 'id', sortable: true, align: 'left' },
    { name: 'name', label: 'Name', field: 'name', sortable: true, align: 'left' },
    { name: 'public_key', label: 'Public Key', field: 'public_key', align: 'left' },
    { name: 'servers_count', label: 'Assigned Servers', field: 'servers_count', align: 'center' },
    { name: 'created_at', label: 'Creation Date', sortable: true, field: 'created_at', align: 'left' },
    { name: 'actions', label: 'Actions', field: 'actions', align: 'center' }
];

const filters = [
    {
        name: 'created_at',
        label: 'Creation Date',
        type: 'date',
        placeholder: 'Filter by date'
    }
]

const search = ref('');

const openCreateModal = ref(false);
const openEditModal = ref(false);
const openDeleteConfirmationModal = ref(false);
const openShowModal = ref(false);

const itemToUpdate = ref(null);
const itemToDelete = ref(null); 
const itemToShow = ref(null);

const onFiltersUpdate = ({search, filters}) => {
    fetch({
        search: search,
        filters: filters
    });
};

const searchChange = () => {
    onRequest({
        filter: search.value,
    });
};

const handleShow = (row) => {
    itemToShow.value = row;

    openShowModal.value = true;
};

const handleEdit = (row) => {
    itemToUpdate.value = row;

    openEditModal.value = true;
};

const handleCreated = () => {
    openCreateModal.value = false;

    fetch({
        filter: search.value,
        filters: options.filters,
    });
}

const handleUpdated = () => {
    openEditModal.value = false;
    
    fetch({
        filter: search.value,
        filters: options.filters,
    });
}

// DELETE HANDLERS

const handleConfirmDelete = (row) => {
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
