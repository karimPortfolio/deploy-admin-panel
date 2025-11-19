<template>
    <q-card class="p-4">
        <q-card-section class="p-0 flex items-center gap-2 pb-0">
            <q-icon name="sym_r_backup" color="primary" size="sm" />
            <h4 class="font-meduim text-lg">
                {{ $t("databases.latest_snapshots") }}
            </h4>
        </q-card-section>
        <template v-if="snapshots && snapshots.length">
            <q-card-section>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 justify-between items-center"
                >
                    <template v-for="snapshot in snapshots" :key="snapshot.id">
                        <q-card
                            class="w-full flex flex-nowrap gap-3 items-center p-3 border mt-3"
                        >
                            <q-card-section class="p-0">
                                <div class="p-2 w-fit rounded-md bg-primary-50">
                                    <q-icon
                                        name="sym_r_backup"
                                        size="md"
                                        class="text-primary"
                                    />
                                </div>
                            </q-card-section>

                            <q-card-section class="p-0 flex-1">
                                <!-- ====== SNAPSHOT IDENTIFIER ===== -->
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium">
                                        {{ snapshot.snapshot_identifier }}
                                    </div>
                                </div>

                                <!-- ====== TYPE & DATE CREATED ====== -->
                                <div
                                    class="flex flex-nowrap items-center gap-1 text-xs text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    <div v-if="snapshot.snapshot_type">
                                        {{
                                            getTranslatedSnapshotType(
                                                snapshot.snapshot_type
                                            )
                                        }}
                                    </div>
                                    <div v-else>N/A</div>
                                    |
                                    <div>{{ snapshot.created_at }}</div>
                                </div>

                                <!-- ===== STATUS ==== -->
                                <div class="w-fit mt-1">
                                    <q-badge
                                        :text-color="snapshot.status.color"
                                        size="xs"
                                        class="q-mt-xs"
                                    >
                                        {{ snapshot.status.label }}
                                    </q-badge>
                                </div>
                            </q-card-section>
                        </q-card>
                    </template>
                </div>
            </q-card-section>
        </template>
        <template v-else>
            <q-card-section class="pt-4 px-2">
                <warning-alert message="no_data_available_msg" />
            </q-card-section>
        </template>
        <q-card-section v-if="loading" class="p-0">
            <q-inner-loading :showing="loading" class="rounded-lg">
                <q-spinner-tail color="primary" size="40px" />
            </q-inner-loading>
        </q-card-section>
    </q-card>
</template>
<script setup>
import WarningAlert from "@/components/alerts/WarningAlert.vue";
import { useMemoize } from "@vueuse/core";
import { useI18n } from "vue-i18n";

const props = defineProps({
    snapshots: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update", "delete"]);

const { t } = useI18n();

const getTranslatedSnapshotType = useMemoize((type) => {
    switch (type) {
        case "automatic":
            return t("databases.automatic_snapshot");
        case "manual":
            return t("databases.manual_snapshot");
        default:
            return "N/A";
    }
});
</script>
