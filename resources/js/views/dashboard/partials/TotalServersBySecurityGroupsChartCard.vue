<template>
    <q-card class="shadow-none">
        <q-card-section class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <q-icon name="sym_r_host" color="warning" size="sm" />
                <div class="font-medium">Total Servers By Security Groups</div>
            </div>
            <!-- <div>
                <q-select
                    :options="yearsOptions"
                    v-model="selectedYear"
                    outlined
                    emit-value
                    map-options
                    dense
                    @update:model-value="handleYearSelectUpdate"
                />
            </div> -->
        </q-card-section>
        <q-card-section class="p-0 pb-4">
            <apexchart
                type="donut"
                height="250"
                :options="chartOptions"
                :series="series"
            />
        </q-card-section>
    </q-card>
</template>
<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { useResourceIndex } from "@/composables/useResourceIndex";
import { useTextTruncate } from "@/composables/useTextTruncate";
import { useQuasar } from "quasar";


const { data, fetch, loading } = useResourceIndex(
    "dashboard/servers-by-security-groups"
);

const { truncate } = useTextTruncate();
 
const $q = useQuasar();

const labels = computed(() => {
    if (!data.value || !Array.isArray(data.value)) return [];

    return data.value.map((v) => {
        return v.securityGroup;
    });
});

const chartOptions = computed(() => {

  return {
    chart: {
      type: 'donut',
      height: 250,
      animations: {
        enabled: true,
        speed: 800,
        animateGradually: {
          enabled: true,
          delay: 150
        },
        dynamicAnimation: {
          enabled: true,
          speed: 350
        }
      },
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
      background: 'transparent',
    },
    plotOptions: {
      pie: {
        startAngle: -90,
        endAngle: 270,
        expandOnClick: false
      }
    },
    fill: {
      type: 'gradient',
    },
    dataLabels: {
      enabled: true,
      formatter: function (val) {
        val = val.toFixed(0);

        return val+'%';
      },
    },
    stroke: {
      curve: "smooth",
    },
    labels: labels.value ?? [],
    legend: {
      formatter: function (val) {
        return truncate(val, 15);
      },
    }
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
