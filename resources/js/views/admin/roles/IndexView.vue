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

        <assign-permission-form
            v-model:open="openAssignPermissionsModal"
            :id="itemToAssignPermissions?.id"
            @assigned="handleUpdated"
        />

        <show-details-view
            v-model:open="openShowModal"
            :id="itemToShow?.id"
        />

        <confirmation-modal
            v-model:open="openDeleteConfirmationModal"
            title="roles.delete"
            icon="warning"
            color="negative"
            :loading="destroying"
            @confirm="handleDelete"
            @cancel="openDeleteConfirmationModal = false"
        />

        <!-- ===== PAGE CONTENT === -->
        <page-header
            title="roles.title"
            subtitle="roles.admin_subtitle"
            icon="admin_panel_settings"
            actionLabel="roles.create"
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
                :rows="rolesData ?? []"
                :loading="loading"
                v-model:pagination="options.pagination"
                @request="onRequest"
                flat
                :rows-per-page-options="[5, 10, 20, 30, 40, 50, 100]"
            >
                <template v-slot:body-cell-permissions_count="props">
                    <q-td :props="props">
                        <q-badge text-color="primary" class="w-fit text-sm">
                            <q-icon name="sym_r_shield_locked" size="16px" class="inline-block mr-1" />
                            {{ props.row.permissions_count }}
                        </q-badge>
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <actions-column
                            :row="props.row"
                            @show-details="handleShow"
                            @update="handleUpdate"
                            @delete="handleConfirmDelete"
                            @assign-permissions="handleAssignPermissions"
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
import { computed, ref } from "vue";
import ActionsColumn from "./table-columns/ActionsColumn.vue";
import ConfirmationModal from "@/components/modals/ConfirmationModal.vue";
import ShowDetailsView from "./ShowDetailsView.vue";
import AssignPermissionForm from "./AssignPermissionForm.vue";
import CreateForm from "./CreateForm.vue";
import EditForm from "./EditForm.vue";
import { useI18n } from "vue-i18n";

const {
    data: rolesData,
    fetch,
    loading,
    options,
    onRequest
} = useResourceIndex('admin/roles');

const { destroy, destroyed, destroying } =
    useResourceDestroy("admin/roles");

const { t } = useI18n();

const columns = computed(() => [
    { name: 'id', label: t("id"), field: 'id', sortable: true, align: 'left' },
    { name: 'name', label: t("name"), field: 'name', sortable: true, align: 'left' },
    { name: 'permissions_count', label: t("roles.permissions_count"), field: 'permissions_count', align: 'left' },
    { name: 'created_at', label: t("creation_details"), sortable: true, field: 'created_at', align: 'left' },
    { name: 'actions', label: t("actions"), field: 'actions', align: 'center' }
]);

const filters = computed(() => [
    {
        name: 'created_at',
        label: t('creation_date'),
        type: 'date',
    }
]);


const search = ref('');

const openDeleteConfirmationModal = ref(false);
const openCreateModal = ref(false);
const openEditModal = ref(false);
const openShowModal = ref(false);
const openAssignPermissionsModal = ref(false);

const itemToDelete = ref(null); 
const itemToShow = ref(null);
const itemToUpdate = ref(null);
const itemToAssignPermissions = ref(null);

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

const handleUpdate = (row) => {
    itemToUpdate.value = row;

    openEditModal.value = true;
};

const handleAssignPermissions = (row) => {
    itemToAssignPermissions.value = row;

    openAssignPermissionsModal.value = true;
};

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

const handleUpdated = () => {
    openEditModal.value = false;

    fetch({
        filter: search.value,
        filters: options.filters,
    });
};

const handleCreated = () => {
    openCreateModal.value = false;

    fetch({
        filter: search.value,
        filters: options.filters,
    });
};

const handleDeleted = () => {
    openDeleteConfirmationModal.value = false;

    fetch({
        filter: search.value,
        filters: options.filters,
    });
};
</script>

