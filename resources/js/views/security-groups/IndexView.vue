<template>
    <q-page class="q-pa-md">
        <page-header
            title="Security Groups"
            subtitle="Manage your security groups"
            icon="security"
            actionLabel="Create Security Group"
            actionIcon="sym_r_add"
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
                <filter-panel
                    :filters="filters"
                    @update="onFiltersUpdate"
                />
            </div>

            <!-- ======== TABLE ========= -->
            <q-table
                :columns="columns"
                :rows="securityGroupsData ?? []"
                :loading="loading"
                v-model:pagination="options.pagination"
                @request="onRequest"
                flat
                ref="tableRef"
                :rows-per-page-options="[10, 20, 30, 40, 50, 100]"
            >
                <template v-slot:body-cell-servers_count="props">
                    <q-td :props="props">
                        {{ props.row.servers_count }}
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <actions-column :row="props.row" />
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
import FilterPanel from "../../components/FilterPanel.vue";
import SearchBar from "../../components/SearchBar.vue";
import ActionsColumn from "./table-columns/ActionsColumn.vue";

const columns = [
    {
        name: "id",
        label: "id",
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
        name: "description",
        label: "Description",
        field: "description",
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
        label: "Servers Count",
        field: "servers_count",
        align: "center",
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
];

const search = ref("");

const {
    data: securityGroupsData,
    loading,
    fetch,
    options,
    onRequest,
} = useResourceIndex("security-groups");

const searchChange = () => {
    onRequest({
        filter: search.value,
    });
};

function onFiltersUpdate({ search, filters }) {
    fetch({ filter: search, filters });
}

const handleUpdated = ()  => {
    fetch();
}

const handleCreated = () => {
    fetch();
}

const handleDeleted = () => {
    fetch();
}

// onMounted(() => fetch());
</script>
