<template>
    <q-page class="q-pa-md">
        <!-- <apexchart width="500" type="bar" :options="options" :series="series"></apexchart> -->
        <q-card class="p-4">
            <q-card-section class="p-0 flex items-center gap-2 pb-4">
                <q-icon name="sym_r_host" color="primary" size="sm" />
                <h4 class="font-meduim text-lg">Server details</h4>
            </q-card-section>
            <q-card-section
                class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 sm:gap-y-0 p-0"
            >
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>Name</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.name }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>Instance ID</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.instance_id }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>Security Group</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.security_group?.group_id }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>VPC</div>
                    <div
                        v-if="server?.vpc_id"
                        class="text-gray-600 dark:text-gray-400"
                    >
                        {{ server?.vpc_id }}
                    </div>
                    <div v-else class="text-gray-600 dark:text-gray-400">
                        N/A
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>Instance Type</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.instance_type?.value }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>OS Family</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.os_family?.label }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>Image ID</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.image_id }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>Public IP Address</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ server?.public_ip_address }}
                    </div>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>Status</div>
                    <q-badge
                        :text-color="server?.status?.color"
                        size="xs"
                        class="w-fit"
                    >
                        {{ server?.status?.label }}
                    </q-badge>
                </div>
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>SSH Key</div>
                    <div
                        v-if="server?.ssh_key?.name"
                        class="text-gray-600 dark:text-gray-400"
                    >
                        {{ server?.ssh_key?.name }}
                    </div>
                    <div v-else class="text-gray-600 dark:text-gray-400">
                        N/A
                    </div>
                </div>
            </q-card-section>
            <q-card-secttion>
                <q-inner-loading :showing="loading" class="rounded-lg" >
                    <q-spinner-tail color="primary" size="40px" />
                </q-inner-loading>
            </q-card-secttion>
        </q-card>
    </q-page>
</template>
<script setup>
import { computed, onMounted, watch } from "vue";
import PageHeader from "@/components/PageHeader.vue";
import { useResourceShow } from "@/composables/useResourceShow";
import { useTextTruncate } from "@/composables/useTextTruncate";
import { useRoute } from "vue-router";

const { data: server, fetch, loading } = useResourceShow("servers");

const route = useRoute();

const { truncate } = useTextTruncate();

const options = computed(() => ({
    chart: {
        id: "basic-bar",
    },
    xaxis: {
        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
    },
}));

const series = computed(() => [
    {
        name: "Servers",
        data: [30, 40, 45, 50, 49, 60, 70],
    },
]);

onMounted(() => {
    const serverId = route.params.id;
    if (serverId) {
        fetch(serverId);
    }
});
</script>
