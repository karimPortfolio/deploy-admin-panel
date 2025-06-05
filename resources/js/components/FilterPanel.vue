<template>
    <div class="flex flex-col items-center">
        <!--==== Mobile Toggle Button =====-->
        <div class="flex justify-between items-center">
            <q-btn
                class="md:hidden"
                icon="filter_list"
                label="Filters"
                @click="drawerOpen = true"
                flat
            />
        </div>

        <!-- ========== DESKTOP FILTER ============ -->
        <div v-if="!isMobile" class="flex justify-end items-center gap-4">
            <q-btn
                ref="dropdownRef"
                icon="sym_r_filter_list"
                label="Filters"
                class="q-mr-md"
                outline
                @click="$q.popupProxy?.toggle(dropdownRef)"
            />

            <q-popup-proxy
                transition-show="jump-down"
                transition-hide="jump-up"
                anchor="bottom left"
                self="top left"
                :offset="[-15, 5]"
                class="shadow-lg border dark:border-none rounded-lg"
            >
                <q-card
                    class="q-pa-md dark:bg-slate-700"
                    style="min-width: 300px; max-width: 350px"
                >
                    <div class="flex flex-col gap-3">
                        <div class="font-medium" >Filter by</div>
                        <FilterField
                            v-for="filter in filtersList"
                            :key="filter.name"
                            :filter="filter"
                            v-model="form[filter.name]"
                            class="w-full"
                        />
                    </div>

                    <q-separator class="q-my-md" />

                    <div class="flex flex-nowrap justify-between gap-2">
                        <q-btn
                            label="Clear Filters"
                            color="negative"
                            unelevated
                            flat
                            @click="clearFilters"
                        />
                        <q-btn
                            label="Apply Filters"
                            icon="sym_r_filter_alt"
                            color="primary"
                            unelevated
                            @click="sync"
                        />
                    </div>
                </q-card>
            </q-popup-proxy>
        </div>

        <!-- ========== MOBILE FILTER DRAWER ============ -->
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

            <div class="grid gap-4 mt-4">
                <FilterField
                    v-for="filter in filtersList"
                    :key="filter.name"
                    :filter="filter"
                    v-model="form[filter.name]"
                />
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
import { ref, reactive, computed, onMounted } from "vue";
import { useQuasar } from "quasar";
import FilterField from "./filters/FilterField.vue";
import { useFilterHandler } from "../composables/useFiltersHandler";

const props = defineProps({
    filters: Array,
});

const emit = defineEmits(["update"]);

const $q = useQuasar();

const drawerOpen = ref(false);
const dropdownRef = ref(null);

const isMobile = computed(() => $q.screen.lt.md);
const filtersList = computed(() => props.filters || []);

const form = reactive({});

const { search, sync, clearFilters, initializeFromQuery } = useFilterHandler(
    filtersList,
    form,
    (query) => {
        emit("update", { search: search.value, filters: query });
        drawerOpen.value = false;
        $q.popupProxy?.hide(dropdownRef);
    }
);

onMounted(() => {
    initializeFromQuery();
});
</script>
