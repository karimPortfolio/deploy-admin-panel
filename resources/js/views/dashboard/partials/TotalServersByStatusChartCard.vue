<template>
    <q-card class="shadow-none">
        <q-card-section>
            <div class="flex items-center gap-2">
                <q-icon name="sym_r_host" color="purple" size="sm" />
                <div class="font-medium" :title="$t('servers.total_by_status')" >{{ truncate($t("servers.total_by_status"), 40) }}</div>
            </div>
        </q-card-section>
        <q-card-section v-if="loading || series.length" class="p-0">
            <apexchart
                type="donut"
                height="250"
                :options="chartOptions"
                :series="series"
            />
        </q-card-section>
        <q-card-section v-else class="pb-0">
            <warning-alert
                message="no_data_available_msg"
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
import { useTextTruncate } from "@/composables/useTextTruncate";
import { useQuasar } from "quasar";
import WarningAlert from "@/components/alerts/WarningAlert.vue";
import { useI18n } from "vue-i18n";


const { data, fetch, loading } = useResourceIndex(
    "dashboard/servers-by-status"
);

const { truncate } = useTextTruncate();

const $q = useQuasar();

const labels = computed(() => {
    if (!data.value || !Array.isArray(data.value)) return [];

    return data.value.map((v) => {
        return v.status;
    });
});

const colors = computed(() => {
    if (!data.value || !Array.isArray(data.value)) return [];

    return data.value.map((v) => {
        return v.color;
    });
});

const chartOptions = computed(() => {
    return {
        chart: {
            type: "donut",
            height: 250,
            animations: {
                enabled: true,
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150,
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350,
                },
            },
            zoom: {
                enabled: false,
            },
            toolbar: {
                show: false,
            },
            background: "transparent",
        },
        plotOptions: {
            pie: {
                startAngle: -90,
                endAngle: 270,
                expandOnClick: false,
            },
        },
        fill: {
            type: $q.dark.isActive ? null : "gradient",
            colors: colors.value,
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                val = val.toFixed(0);

                return val + "%";
            },
        },
        stroke: {
            show: false,
        },
        labels: labels.value ?? [],
        legend: {
            formatter: function (val) {
                return val;
            },
        },
        theme: {
            mode: $q.dark.isActive ? "dark" : "light",
        },
    };
});

const series = computed(() => {
    if (!data.value || !Array.isArray(data.value)) return [];

    return data.value.map((v) => {
        return v.total;
    });
});

onMounted(() => {
    fetch();
});
</script>
