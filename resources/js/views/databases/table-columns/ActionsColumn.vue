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
                    :to="{ name: 'databases.show', params: { id: row.id } }"
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
                <!-- ============= ATTACH TO SERVER ========= -->
                <!-- ============================ -->
                <q-item
                    clickable
                    v-close-popup
                    class="text-gray-600 dark:text-gray-200 rounded-md"
                    @click="handleAttach(row)"
                >
                    <q-item-section>
                        <q-item-label class="flex-nowrap">
                            <q-icon
                                name="sym_r_link"
                                size="xs"
                                class="me-1"
                            />
                            {{ $t("attach_to_server") }}
                        </q-item-label>
                    </q-item-section>
                </q-item>


                <!-- ============================ -->
                <!-- ============= CREATE SNAPSHOT ========= -->
                <!-- ============================ -->
                <q-item
                    clickable
                    v-close-popup
                    class="text-gray-600 dark:text-gray-200 rounded-md"
                    @click="handleCreateSnapshot(row)"
                >
                    <q-item-section>
                        <q-item-label class="flex-nowrap">
                            <q-icon
                                name="sym_r_backup"
                                size="xs"
                                class="me-1"
                            />
                            {{ $t("databases.create_snapshot") }}
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

const emit = defineEmits(["delete","attach","create-snapshot"]);

const handleDelete = (row) => {
    emit("delete", row);
};

const handleAttach = (row) => {
    emit("attach", row);
};

const handleCreateSnapshot = (row) => {
    emit("create-snapshot", row);
};

</script>
