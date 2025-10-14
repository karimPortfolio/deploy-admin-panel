<template>
    <q-card class="shadow-none">
        <q-card-section class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <q-icon name="sym_r_security" color="green" size="sm" />
                <div class="font-medium" :title="$t('security_groups.monthly_total')" >{{ truncate($t("security_groups.monthly_total") ,40) }}</div>
            </div>
            <div>
                <q-select
                    :options="yearsOptions"
                    v-model="selectedYear"
                    outlined
                    emit-value
                    map-options
                    dense
                    @update:model-value="handleYearSelectUpdate"
                />
            </div>
        </q-card-section>
        <q-card-section class="p-0">
            <apexchart
                type="area"
                height="250"
                :options="chartOptions"
                :series="series"
            />
        </q-card-section>
        <q-inner-loading :showing="loading">
            <q-spinner-tail color="primary" size="40px" />
        </q-inner-loading>
    </q-card>
</template>
<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { useResourceIndex } from "@/composables/useResourceIndex";
import { useQuasar } from "quasar";
import { useTextTruncate } from "@/composables/useTextTruncate";
import { useI18n } from "vue-i18n";

const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);

const { data, fetch, loading } = useResourceIndex(
    "dashboard/monthly-security-groups-total"
);

const { truncate } = useTextTruncate();
const { t } = useI18n();

const $q = useQuasar();

const categories = computed(() => {
    if (!data.value || !Array.isArray(data.value)) return [];

    return data.value.map((v) => {
        return t(`months.${v.month}`);
    });
});

const chartOptions = computed(() => {
    return {
        chart: {
            type: "area",
            height: 250,
            background: $q.dark.isActive ? "#1e293b" : "#00000",
            toolbar: {
                show: false,
            },
            zoom: {
                enabled: true,
                type: "x",
                autoScaleYaxis: false,
                allowMouseWheelZoom: true,
                zoomedArea: {
                    fill: {
                        color: "#90CAF9",
                        opacity: 0.4,
                    },
                    stroke: {
                        color: "#0D47A1",
                        opacity: 0.4,
                        width: 1,
                    },
                },
            },
        },
        grid: {
            borderColor: "#1976d212",
            yaxis: {
                lines: {
                    show: true,
                },
            },
            xaxis: {
                lines: {
                    show: true,
                },
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: "smooth",
        },
        xaxis: {
            type: "category",
            categories: categories.value ?? [],
        },
        yaxis: {
            opposite: false,
        },
        legend: {
            horizontalAlign: "left",
        },
        theme: {
            mode: $q.dark.isActive ? "dark" : "light",
        },
    };
});

const series = computed(() => {
    if (!data.value || !Array.isArray(data.value)) return [];

    const values = data.value.map((v) => {
        return v.total;
    });

    return [
        {
            name: t("security_groups.title"),
            color: "#00a63e",
            data: values,
        },
    ];
});

const yearsOptions = computed(() => {
    const years = [];

    for (let year = currentYear - 20; year <= currentYear; year++) {
        years.push(year);
    }

    return years;
});

const handleYearSelectUpdate = () => {
    fetch({
        filters: {
            year: selectedYear.value,
        },
    });
};

onMounted(() => {
    fetch({
        filters: {
            year: selectedYear.value,
        },
    });
});
</script>
