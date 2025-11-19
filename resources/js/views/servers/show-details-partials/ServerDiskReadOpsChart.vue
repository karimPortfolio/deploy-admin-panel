<template>
    <apexchart
        height="300"
        type="area"
        :options="options"
        :series="series"
    ></apexchart>
</template>
<script setup>
import { useQuasar } from "quasar";
import { computed } from "vue";

const props = defineProps({
    diskReadOpsData: {
        type: Array,
        default: () => [],
    },
});

const $q = useQuasar();

const sortedData = computed(() => {
    const data = Array.isArray(props.diskReadOpsData) ? props.diskReadOpsData : [];

    return [...data]
        .filter((item) => item && (item.Timestamp || item.TimeStamp))
        .sort((a, b) => {
            const aTimestamp = a.Timestamp ?? a.TimeStamp;
            const bTimestamp = b.Timestamp ?? b.TimeStamp;

            return new Date(aTimestamp) - new Date(bTimestamp);
        });
});

const series = computed(() => {
    return [
        {
            name: "Disk Read Ops",
            color: "#00a63e",
            data: sortedData.value.map((item) => {
                const average = Number(item.Average ?? 0);

                return Number.isFinite(average)
                    ? Number(average.toFixed(2))
                    : 0;
            }),
        },
    ];
});

const categories = computed(() => {
    return sortedData.value.map((dataPoint) => {
        const timestamp = dataPoint.Timestamp ?? dataPoint.TimeStamp;

        if (!timestamp) {
            return "Unknown";
        }

        const date = new Date(timestamp);

        return Number.isNaN(date.getTime())
            ? "Unknown"
            : date.toLocaleString("en-US", {
                  hour: "2-digit",
                  minute: "2-digit",
              });
    });
});


const options = computed(() => {
    return {
        chart: {
            type: "area",
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
        title: {
            text: "Disk Read Operations",
            align: "left",
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
            title: {
                text: "Read Ops (%)",
            },
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
</script>
