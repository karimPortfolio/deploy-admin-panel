<template>
    <div class="flex justify-end">
        <q-btn
            dense
            flat
            icon="sym_r_settings"
            class="hover:bg-gray-100 dark:hover:bg-gray-600"
        >
            <q-menu
                :offset="[0, 6]"
                class="q-pa-xs rounded-lg dark:bg-slate-700 shadow-md border dark:border-0 w-52"
            >
                <div class="p-2 flex flex-nowrap items-center justify-between text-sm" >
                    Dark mode:
                    <div>
                        <q-btn
                            dense
                            flat
                            size="sm"
                            padding="sm"
                            icon="dark_mode"
                            :label="$q.dark.isActive ? 'ON' : 'OFF'"
                            @click="handleDarkToggling"
                        />
                    </div>
                </div>
            </q-menu>
        </q-btn>
    </div>
</template>
<script setup>
import { useQuasar } from 'quasar';



const $q = useQuasar();

const handleDarkToggling = () => {
    $q.dark.toggle();
    localStorage.setItem("dark", $q.dark.isActive);
};
</script>
