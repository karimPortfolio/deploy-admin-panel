<template>
    <q-page class="q-pa-md">

        <!-- ======= INCLUDES ====== -->
        <show-details-view
            v-model:open="openShowModal"
            :id="itemToShow?.id"
        />

        <confirmation-modal
            v-model:open="openDeleteConfirmationModal"
            title="ssh_keys.delete"
            icon="warning"
            color="negative"
            :loading="destroying"
            @confirm="handleDelete"
            @cancel="openDeleteConfirmationModal = false"
        />

        <!-- ===== PAGE CONTENT === -->
        <page-header
            title="ssh_keys.title"
            subtitle="ssh_keys.admin_subtitle"
            icon="key"
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
                <template v-slot:body-cell-created_at="props">
                    <q-td :props="props" :title="props.row.created_by?.name" >
                        {{ props.row.created_at }}
                        <small class="block" >{{ $t("by") }}: <span class="font-medium">{{ truncate(props.row.created_by?.name, 20) }}</span></small>
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <actions-column
                            :row="props.row"
                            @show-details="handleShow"
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
import { useTextTruncate } from "@/composables/useTextTruncate";
import FilterPanel from "@/components/FilterPanel.vue";
import SearchBar from "@/components/SearchBar.vue";
import { computed, ref } from "vue";
import ActionsColumn from "./table-columns/ActionsColumn.vue";
import ConfirmationModal from "@/components/modals/ConfirmationModal.vue";
import ShowDetailsView from "./ShowDetailsView.vue";
import { useI18n } from "vue-i18n";

const {
    data: sshKeysData,
    fetch,
    loading,
    options,
    onRequest
} = useResourceIndex('admin/ssh-keys');

const { destroy, destroyed, destroying } =
    useResourceDestroy("admin/ssh-keys");

const { t } = useI18n();

const columns = computed(() => [
    { name: 'id', label: t("id"), field: 'id', sortable: true, align: 'left' },
    { name: 'name', label: t("name"), field: 'name', sortable: true, align: 'left' },
    { name: 'public_key', label: t("ssh_keys.public_key"), field: 'public_key', align: 'left' },
    { name: 'servers_count', label: t("assigned_servers"), field: 'servers_count', align: 'center' },
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

const { truncate } = useTextTruncate();

const search = ref('');

const openDeleteConfirmationModal = ref(false);
const openShowModal = ref(false);

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
