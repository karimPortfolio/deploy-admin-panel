<template>
    <div class="flex flex-col items-center">
        <!-- Toggle button on mobile -->
        <div class="flex justify-between items-center">
            <q-btn
                class="md:hidden"
                icon="filter_list"
                label="Filters"
                @click="drawerOpen = true"
                flat
            />
        </div>

        <div v-if="!isMobile" class="flex justify-end items-center gap-4">
            <div class="flex items-center gap-4">
                <template v-for="filter in filtersList" :key="filter.name">
                    <FilterField :filter="filter" v-model="form[filter.name]" class="w-[150px]" />
                </template>
            </div>

            <div class="flex justify-end gap-2">
                <q-btn 
                    label="Clear Filters" 
                    color="negative" 
                    icon="sym_r_filter_list_off" 
                    outline
                    @click="clearFilters" 
                />
                <q-btn 
                    label="Apply Filters" 
                    icon="sym_r_filter_list" 
                    unelevated
                    outline
                    @click="sync" 
                />
            </div>
        </div>

        <q-drawer
            v-model="drawerOpen"
            side="right"
            overlay
            behavior="mobile"
            class="bg-white p-4"
        >
            <div class="flex justify-between items-center mb-4">
                <div class="text-lg font-semibold">Filters</div>
                <q-btn flat icon="close" @click="drawerOpen = false" />
            </div>

            <q-input
                dense
                filled
                clearable
                debounce="400"
                v-model="search"
                label="Search"
            />

            <div class="grid gap-4 mt-4">
                <template v-for="filter in filtersList" :key="filter.name">
                    <FilterField :filter="filter" v-model="form[filter.name]" />
                </template>
            </div>

            <div class="flex justify-between gap-2 mt-6">
                <q-btn
                    label="Clear Filters"
                    color="negative"
                    outline
                    class="w-full"
                    icon="sym_r_filter_list_off" 
                    @click="clearFilters"
                />
                <q-btn
                    label="Apply Filters"
                    color="primary"
                    class="w-full"
                    icon="sym_r_filter_list"
                    @click="sync"
                />
            </div>
        </q-drawer>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import FilterField from "./filters/FilterField.vue";
import { useQuasar } from "quasar";
import CustomButton from "./CustomButton.vue";

const props = defineProps({
    filters: Array,
});

const emit = defineEmits(["update"]);

const route = useRoute();
const router = useRouter();
const $q = useQuasar();

const search = ref("");
const form = ref({});

const drawerOpen = ref(false);
const isMobile = computed(() => $q.screen.lt.md);

const filtersList = computed(() => props.filters || []);

function initializeFromQuery() {
    const query = route.query;
    search.value = query.search || "";

    for (const filter of filtersList.value) {
        if (filter.type === "range") {
            form.value[`${filter.name}_min`] =
                query[`filter[${filter.name}_min]`] ?? null;
            form.value[`${filter.name}_max`] =
                query[`filter[${filter.name}_max]`] ?? null;
        } else {
            form.value[filter.name] = query[`filter[${filter.name}]`] ?? null;
        }
    }
}

function buildQuery() {
    const filterObj = {};

    for (const filter of filtersList.value) {
        if (filter.type === "range") {
            if (form.value[`${filter.name}_min`] != null)
                filterObj[`${filter.name}_min`] =
                    form.value[`${filter.name}_min`];
            if (form.value[`${filter.name}_max`] != null)
                filterObj[`${filter.name}_max`] =
                    form.value[`${filter.name}_max`];
        } else {
            if (
                form.value[filter.name] != null &&
                form.value[filter.name] !== ""
            )
                filterObj[filter.name] = form.value[filter.name];
        }
    }

    return {
        search: search.value || undefined,
        filter: filterObj,
    };
}

function sync() {
    const query = buildQuery();
    const flatQuery = {
        ...route.query,
        search: query.search,
        ...Object.keys(query.filter).reduce((acc, key) => {
            acc[`filter[${key}]`] = query.filter[key];
            return acc;
        }, {}),
    };

    // Remove empty filters
    for (const key in flatQuery) {
        if (
            flatQuery[key] === undefined ||
            flatQuery[key] === "" ||
            flatQuery[key] === null
        ) {
            delete flatQuery[key];
        }
    }

    router.replace({ query: flatQuery });
    emit("update", { search: search.value, filters: query.filter });

    drawerOpen.value = false;
}

function clearFilters() {
    search.value = "";
    for (const filter of filtersList.value) {
        if (filter.type === "range") {
            form.value[`${filter.name}_min`] = null;
            form.value[`${filter.name}_max`] = null;
        } else {
            form.value[filter.name] = null;
        }
    }
    sync();
}

onMounted(() => {
    initializeFromQuery();
});
</script>
