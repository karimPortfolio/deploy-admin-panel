<template>
    <q-btn icon="sym_r_more_horiz" padding="xs" size="sm" unelevated>
        <q-menu
            :offset="[15, 6]"
            transition-show="jump-down"
            transition-hide="jump-up"
            class="q-pa-xs rounded-lg dark:bg-slate-700 shadow-lg border dark:border-none min-w-48"
        >
            <q-list style="min-width: 100px" dense class="q-pa-xs">
                <!-- ============================ -->
                <!-- ============= VIEW ========= -->
                <!-- ============================ -->
                <q-item
                    @click="handleShow(row)"
                    clickable
                    v-close-popup
                    class="text-gray-600 dark:text-gray-200 rounded-md"
                >
                    <q-item-section>
                        <q-item-label class="flex-nowrap">
                            <q-icon
                                name="sym_r_visibility"
                                size="xs"
                                class="me-1"
                            />
                            {{ $t("view_details") }}
                        </q-item-label>
                    </q-item-section>
                </q-item>

                <!-- ============================ -->
                <!-- ============= STOP SERVER ========= -->
                <!-- ============================ -->
                <q-item
                    v-if="row.is_active"
                    @click="statusChange(row, 'deactivate')"
                    clickable
                    v-close-popup
                    class="text-red-600 dark:text-red-400 rounded-md"
                >
                    <q-item-section>
                        <q-item-label class="flex-nowrap">
                            <q-icon
                                name="sym_r_cancel"
                                size="xs"
                                class="me-1"
                            />
                            {{ $t("deactivate") }}
                        </q-item-label>
                    </q-item-section>
                </q-item>

                <!-- ============================ -->
                <!-- ============= START SERVER ========= -->
                <!-- ============================ -->
                <q-item
                    v-else
                    @click="statusChange(row, 'activate')"
                    clickable
                    v-close-popup
                    class="text-green-600 dark:text-green-400 rounded-md"
                >
                    <q-item-section>
                        <q-item-label class="flex-nowrap">
                            <q-icon
                                name="sym_r_play_circle"
                                size="xs"
                                class="me-1"
                            />
                            {{ $t("activate") }}
                        </q-item-label>
                    </q-item-section>
                </q-item>
              
                <q-separator class="q-my-xs" />

                <!-- ============================ -->
                <!-- =========== DELETE ========= -->
                <!-- ============================ -->
                <q-item
                    @click="handleDelete(row)"
                    clickable
                    v-close-popup
                    class="text-red-600 dark:text-red-400 rounded-md"
                >
                    <q-item-section>
                        <q-item-label class="flex-nowrap">
                            <q-icon
                                name="sym_r_delete"
                                size="xs"
                                class="me-1"
                            />
                            {{ $t("delete") }}
                        </q-item-label>
                    </q-item-section>
                </q-item>
            </q-list>
        </q-menu>
    </q-btn>
</template>
<script setup>
const props = defineProps({
    row: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["delete","show-details", "status-change"]);

const handleDelete = (row) => {
    emit("delete", row);
};

const statusChange = (row, status) => {
    emit("status-change", row, status);
}

const handleShow = (row) => {
    emit("show-details", row);
};
</script>
