<template>
    <q-page class="q-pa-md">
        <!-- ========= INCLUDES ========= -->
        <create-form
            v-model:open="openCreateModal"
            @created="handleCreated"
        />

        <attach-to-server-form
            v-model:open="openAttachModal"
            :id="itemToAttach?.id"
            @attached="handleAttached"
        />

        <confirmation-modal
            v-model:open="openDeleteConfirmationModal"
            title="databases.delete"
            icon="warning"
            color="negative"
            :loading="destroying"
            @confirm="handleDelete"
            @cancel="openDeleteConfirmationModal = false"
        />
        

        <!-- ===== PAGE CONTENT === -->
        <page-header
            title="databases.title"
            subtitle="databases.subtitle"
            icon="sym_r_database"
            actionLabel="databases.create"
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
                :rows="rdsDatabases ?? []"
                :loading="loading"
                v-model:pagination="options.pagination"
                @request="onRequest"
                flat
                :rows-per-page-options="[5, 10, 20, 30, 40, 50, 100]"
            >
                <template v-slot:body-cell-db_name="props">
                    <q-td :props="props">
                        <DBNameColumn :row="props.row" />
                    </q-td>
                </template>
                <template v-slot:body-cell-engine="props">
                    <q-td :props="props">
                        <engine-column :row="props.row" />
                    </q-td>
                </template>
                <template v-slot:body-cell-storage_type="props">
                    <q-td :props="props">
                        <storage-column :row="props.row" />
                    </q-td>
                </template>
                <template v-slot:body-cell-security_group="props">
                    <q-td :props="props">
                        <security-group-column :row="props.row" />
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
                <template v-slot:body-cell-availability_zone="props">
                    <q-td :props="props">
                        <div v-if="props.row.availability_zone">{{ props.row.availability_zone }}</div>
                        <div v-else class="text-xs" >N/A</div>
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <actions-column
                            :row="props.row"
                            @delete="handleDeleteConfirmation"
                            @attach="handleAttachToServer"
                        />
                    </q-td>
                </template>
            </q-table>
        </div>
        <div class="mt-2 ms-2 text-xs text-gray-500 dark:text-gray-400">
            <span class="font-medium">{{ $t("note") }}:</span> 
            <ul class="list-disc ms-4 mt-1">
                <li>{{ $t("databases.instance_id_details") }}.</li>
                <li>{{ $t("not_available_meaning") }}</li>
            </ul>
        </div>
    </q-page>
</template>
<script setup>
import PageHeader from "@/components/PageHeader.vue";
import { useResourceIndex } from "@/composables/useResourceIndex";
import { useResourceDestroy } from "@/composables/useResourceDestroy";
import FilterPanel from "@/components/FilterPanel.vue";
import SearchBar from "@/components/SearchBar.vue";
import SecurityGroupColumn from "./table-columns/SecurityGroupColumn.vue";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import DBNameColumn from "./table-columns/DBNameColumn.vue";
import EngineColumn from "./table-columns/EngineColumn.vue";
import StorageColumn from "./table-columns/StorageColumn.vue";
import ActionsColumn from "./table-columns/ActionsColumn.vue";
import CreateForm from "./CreateForm.vue";
import ConfirmationModal from "@/components/modals/ConfirmationModal.vue";
import AttachToServerForm from "./AttachToServerForm.vue";

const {
    data: rdsDatabases,
    fetch,
    loading,
    options,
    onRequest,
} = useResourceIndex("rds-databases");

const { destroy, destroyed, destroying } = useResourceDestroy("rds-databases");

const { t } = useI18n();

const columns = computed(() => [
    { name: "id", label: t("id"), field: "id", sortable: true, align: "left" },
    { name: "db_name", label: t("name"), field: "db_name", align: "left" },
    {
        name: "engine",
        label: t("databases.engine"),
        field: "engine",
        align: "left",
    },
    {
        name: "storage_type",
        label: t("databases.storage"),
        field: "storage_type",
        align: "left",
    },
    {
        name: "security_group",
        label: t("databases.security_group"),
        field: "security_group",
        align: "left",
    },
    {
        name: "availability_zone",
        label: t("databases.availability_zone"),
        field: "availability_zone",
        align: "left",
    },
    { name: "status", label: t("status"), field: "status", align: "left" },
    {
        name: "created_at",
        label: t("creation_details"),
        sortable: true,
        field: "created_at",
        align: "left",
    },
    { name: "actions", label: t("actions"), field: "actions", align: "center" },
]);

const filters = computed(() => [
    {
        name: "created_at",
        label: t("creation_date"),
        type: "date",
    },
]);

const search = ref("");

const openCreateModal = ref(false);
const openDeleteConfirmationModal = ref(false);
const openAttachModal = ref(false);

const itemToDelete = ref(null);
const itemToAttach = ref(null);

const onFiltersUpdate = ({ search, filters }) => {
    fetch({
        search: search,
        filters: filters,
    });
};

const searchChange = () => {
    onRequest({
        filter: search.value,
    });
};

const handleCreate = () => {
    openCreateModal.value = true;
};

const handleCreated = () => {
    openCreateModal.value = false;

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


const handleAttachToServer = (row) => {
    itemToAttach.value = row;

    openAttachModal.value = true;
};

const handleAttached = () => {
    openAttachModal.value = false;

    fetch({
        filter: search.value,
        filters: options.filters,
    });
};

</script>
