<template>
    <q-page class="q-pa-md">
        <show-details-view
            v-model:open="openShowDetailsModal"
            :id="itemToShow?.id"
        />

        <create-view
            v-model:open="openCreationModal"
            @created="handleCreated"
        />

        <assign-permission-form
            v-model:open="openAssignPermissionsModal"
            :id="itemToAssignPermissions?.id"
            @updated="handleCreated"
        />

        <confirmation-modal
            v-model:open="openDeleteConfirmationModal"
            title="users.delete"
            icon="warning"
            color="negative"
            :loading="destroying"
            @confirm="handleDelete"
            @cancel="openDeleteConfirmationModal = false"
        />

        <confirmation-modal
            v-model:open="openStatusChangeConfirmationModal"
            :title="userAccountStatusChangeModalTitle"
            icon="info"
            color="warning"
            :actionLabel="userAccountStatusChangeActionLabel"
            :loading="userAccountStatusChangeLoading"
            @confirm="handleStatusChange"
            @cancel="openStatusChangeConfirmationModal = false"
        />

        <page-header
            title="users.title"
            subtitle="users.subtitle"
            icon="sym_r_group"
            actionLabel="users.create"
            actionIcon="sym_r_add"
            :action="() => (openCreationModal = true)"
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
                :rows="users ?? []"
                :loading="loading"
                v-model:pagination="options.pagination"
                @request="onRequest"
                flat
                :rows-per-page-options="[5, 10, 20, 30, 40, 50, 100]"
            >
                <template v-slot:body-cell-name="props">
                    <q-td :props="props" :title="props.row.name">
                        <q-avatar class="w-8 h-8 mr-1">
                            <q-img
                                :src="props.row?.photo ?? '/src/img/avatar.png'"
                            />
                        </q-avatar>
                        {{ props.row.name ?? "N/A" }}
                    </q-td>
                </template>
                <template v-slot:body-cell-company_name="props">
                    <q-td :props="props" :title="props.row.company_name">
                        {{ props.row.company_name ?? "N/A" }}
                    </q-td>
                </template>
                <template v-slot:body-cell-role="props">
                    <q-td :props="props" :title="props.row.role">
                        {{ props.row.role?.label ?? "N/A" }}
                    </q-td>
                </template>
                <template v-slot:body-cell-status="props">
                    <q-td :props="props" :title="props.row.status">
                        <q-badge
                            v-if="props.row.is_active"
                            text-color="positive"
                            label="Active"
                            class="w-fit"
                        />
                        <q-badge
                            v-else
                            text-color="negative"
                            label="Inactive"
                            class="w-fit"
                        />
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <actions-column
                            :row="props.row"
                            @delete="handleDeleteConfirmation"
                            @show-details="handleShowDetails"
                            @status-change="statusChangeConfirmation"
                            @assign-permissions="handlePermissionsAssignment"
                        />
                    </q-td>
                </template>
            </q-table>
        </div>
    </q-page>
</template>
<script setup>
import PageHeader from "@/components/PageHeader.vue";
import { computed, ref } from "vue";
import { useResourceIndex } from "@/composables/useResourceIndex";
import { useResourceUpdate } from "@/composables/useResourceUpdate";
import { useResourceDestroy } from "@/composables/useResourceDestroy";
import FilterPanel from "@/components/FilterPanel.vue";
import SearchBar from "@/components/SearchBar.vue";
import ActionsColumn from "./table-columns/ActionsColumn.vue";
import ConfirmationModal from "@/components/modals/ConfirmationModal.vue";
import ShowDetailsView from "./ShowDetailsView.vue";
import AssignPermissionForm from "./AssignPermissionForm.vue";
import CreateView from "./CreateView.vue";
import { useI18n } from "vue-i18n";

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
        name: "company_name",
        label: t("users.company_name"),
        field: "company_name",
        align: "left",
        sortable: false,
    },
    {
        name: "email",
        label: t("users.email"),
        field: "email",
        align: "left",
        sortable: true,
    },
    {
        name: "role",
        label: t("users.role"),
        field: "role",
        align: "left",
        sortable: false,
    },
    {
        name: "status",
        label: t("status"),
        field: "is_active",
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
        name: "role",
        label: t("users.role"),
        type: "enum",
        resource: "admin/users/roles",
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
const openDeleteConfirmationModal = ref(false);
const openCreationModal = ref(false);
const openShowDetailsModal = ref(false);
const openAssignPermissionsModal = ref(false);

const itemToDelete = ref(null);
const itemToShow = ref(null);
const itemToChangeStatus = ref(null);
const itemToAssignPermissions = ref(null);

const openStatusChangeConfirmationModal = ref(false);
const newUserAccountStatus = ref(false);

const userAccountStatusChangeModalTitle = computed(() => {
    return newUserAccountStatus.value === "activate"
        ? t("users.activate_user_account")
        : t("users.deactivate_user_account");
});

const userAccountStatusChangeActionLabel = computed(() => {
    return newUserAccountStatus.value === "activate"
        ? t("activate")
        : t("deactivate");
});

const userAccountStatusChangeLoading = computed(() => {
    return newUserAccountStatus.value === "activate"
        ? updating.value
        : deactivating.value;
});

const {
    data: users,
    loading,
    fetch,
    options,
    onRequest,
} = useResourceIndex("admin/users");

const {
    update: activate,
    updating,
    validation,
} = useResourceUpdate(
    () => `admin/users/${itemToChangeStatus.value.id}/activate`
);

const {
    update: deactivate,
    updating: deactivating,
    validation: deactivationValidation,
} = useResourceUpdate(
    () => `admin/users/${itemToChangeStatus.value.id}/deactivate`
);

const { destroy, destroying, destroyed } = useResourceDestroy("admin/users");

const searchChange = () => {
    onRequest({
        filter: search.value,
    });
};

const handlePermissionsAssignment = (row) => {
    itemToAssignPermissions.value = row;

    openAssignPermissionsModal.value = true;
};

const handleCreated = () => {
    openCreationModal.value = false;

    fetch({
        filter: search.value,
        filters: options.filters,
    });
};

function onFiltersUpdate({ search, filters }) {
    fetch({ filter: search, filters });
}

const statusChangeConfirmation = (row, status) => {
    itemToChangeStatus.value = row;
    newUserAccountStatus.value = status;

    openStatusChangeConfirmationModal.value = true;
};

const handleShowDetails = (row) => {
    itemToShow.value = row;
    openShowDetailsModal.value = true;
};

const handleStatusChange = async () => {
    if (newUserAccountStatus.value === "activate") {
        await activate();
        openStatusChangeConfirmationModal.value = false;
        newUserAccountStatus.value = null;
        return fetch({
            filter: search.value,
            filters: options.filters,
        });
    }

    await deactivate();
    openStatusChangeConfirmationModal.value = false;
    newUserAccountStatus.value = null;
    fetch({
        filter: search.value,
        filters: options.filters,
    });
};

const handleDeleteConfirmation = (row) => {
    itemToDelete.value = row;

    openDeleteConfirmationModal.value = true;
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
</script>
