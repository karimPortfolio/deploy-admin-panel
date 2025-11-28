<template>
    <q-page class="q-pa-md">
         <q-card class="p-4 pb-0 mb-4">
            <q-card-section class="p-0 flex items-center gap-2 pb-5">
                <q-icon name="sym_r_bar_chart" color="primary" size="sm" />
                <h4 class="font-meduim text-lg">{{ $t("servers.server_monitoring") }}</h4>
            </q-card-section>
            <q-card-section class="p-0 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <server-cpu-usage-chart
                    :cpu-usage-data="statistics?.cpu_utilization ?? []"
                />
                <server-disk-read-ops-chart
                    :diskReadOpsData="statistics?.disk_read_ops ?? []"
                />
                <server-disk-write-ops-chart
                    :diskWriteOpsData="statistics?.disk_write_ops ?? []"
                />
            </q-card-section>
            <q-card-section class="p-0"> 
                <q-inner-loading :showing="!statistics.cpu_utilization" class="rounded-lg" >
                    <q-spinner-tail color="primary" size="40px" />
                </q-inner-loading>
            </q-card-section>
        </q-card> 

        <q-card class="p-4">
            <q-card-section class="p-0 flex items-center gap-2 pb-4">
                <q-icon name="sym_r_host" color="primary" size="sm" />
                <h4 class="font-meduim text-lg">{{ $t("servers.server_details") }}</h4>
            </q-card-section>
            <q-card-section
                class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 sm:gap-y-0 p-0"
            >
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("name") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.name }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("servers.instance_id") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.instance_id ?? 'N/A' }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("security_groups.title") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.security_group?.group_id }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>VPC</div>
                    <div
                        class="text-gray-600 dark:text-gray-400"
                    >
                        {{ server?.vpc_id ?? 'N/A' }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("servers.instance_type") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.instance_type?.value }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("servers.os_family") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.os_family?.label }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("servers.image_id") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.image_id ?? 'N/A' }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("servers.public_ip_address") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.public_ip_address ?? 'N/A' }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("status") }}</div>
                    <q-badge
                        :text-color="server?.status?.color"
                        size="xs"
                        class="w-fit"
                    >
                        {{ server?.status?.label }}
                    </q-badge>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("servers.ssh_key") }}</div>
                    <div
                        class="text-gray-600 dark:text-gray-400"
                    >
                        {{ server?.ssh_key?.name ?? 'N/A' }}
                    </div>
                </div>
            </q-card-section>
            <q-card-section class="p-0"> 
                <q-inner-loading :showing="loading" class="rounded-lg" >
                    <q-spinner-tail color="primary" size="40px" />
                </q-inner-loading>
            </q-card-section>
        </q-card>
    </q-page>
</template>
<script setup>
import { onMounted, ref } from "vue";
import { useResourceShow } from "@/composables/useResourceShow";
import { useRoute } from "vue-router";
import ServerCpuUsageChart from "./show-details-partials/ServerCpuUsageChart.vue";
import ServerDiskReadOpsChart from "./show-details-partials/ServerDiskReadOpsChart.vue";
import ServerDiskWriteOpsChart from "./show-details-partials/ServerDiskWriteOpsChart.vue";

const statistics = ref({
    cpu_utilization: [],
    disk_read_ops: [],
    disk_write_ops: [],
});

const { data: server, fetch, loading } = useResourceShow("servers", {
    onSuccess: (data) => {
        if (data.data && data.data.statistics) {
            statistics.value = data.data.statistics;
        }
    }
});

const route = useRoute();

onMounted(async () => {
    const serverId = route.params.id;
    if (serverId) {
        await fetch(serverId);
    }
});
</script>
