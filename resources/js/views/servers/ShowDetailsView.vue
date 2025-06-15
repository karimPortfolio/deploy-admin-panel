<template>
    <show-modal title="Server Details" :loading="loading" >
        <template #content>
            <q-card class="shadow-none bg-transparent">
                <q-card-section class="p-0 flex justify-center items-center">
                    <div
                        class="bg-primary-50 flex justify-center items-center w-fit p-3 rounded-full"
                    >
                        <q-icon name="sym_r_host" size="xl" color="primary" />
                    </div>
                </q-card-section>
                <q-card-section class="q-pa-md">
                    <div class="text-h6 text-center mb-4">
                        {{ server?.name }}
                    </div>
                    
                    <!-- ======= CREATION DATE ======= -->
                    <q-separator class="mt-2" />
                    <div class="grid grid-cols-2 justify-between mt-3">
                        <div class="text-subtitle2">Creation Date</div>
                        <div class="text-gray-700 dark:text-gray-400 text-end">
                            {{ server?.created_at }}
                        </div>
                    </div>

                   
                </q-card-section>
            </q-card>
        </template>
    </show-modal>
</template>
<script setup>
import { watch } from "vue";
import ShowModal from "../../components/modals/ShowModal.vue";
import { useResourceShow } from "@/composables/useResourceShow";

const props = defineProps({
    id: {
        type: [String, Number],
    },
});

const { data: server, fetch, loading } = useResourceShow("servers");

watch(
    () => props.id,
    (newId) => {
        if (newId) {
            fetch(newId);
        }
    }
);
</script>
