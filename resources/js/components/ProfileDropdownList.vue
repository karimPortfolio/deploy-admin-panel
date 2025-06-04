<template>
    <q-list style="min-width: 150px" dense class="q-pa-xs mt-1">
        <q-item
            target="_blank"
            v-close-popup
            clickable
            class="py-2 hover:cursor-pointer rounded-md "
        >
            <q-item-section avatar class="min-w-fit pe-2">
                <q-icon name="sym_r_person" size="xs" />
            </q-item-section>

            <q-item-section class="text-sm">Profile</q-item-section>
        </q-item>

        <q-item
            clickable
            v-close-popup
            class="py-2 mb-2 hover:cursor-pointer rounded-md "
        >
            <q-item-section avatar class="min-w-fit pe-2">
                <q-icon name="sym_r_settings" size="xs" />
            </q-item-section>

            <q-item-section class="text-sm"> Settings </q-item-section>
        </q-item>

        <q-separator />

        <q-item
            clickable
            @click="handleLogOut"
            v-close-popup
            class="py-2 mt-2 hover:cursor-pointer rounded-md "
        >
            <q-item-section avatar class="min-w-fit pe-2">
                <q-icon name="sym_r_logout" size="xs" />
            </q-item-section>

            <q-item-section class="text-sm"> Sign out </q-item-section>
        </q-item>
    </q-list>
</template>
<script setup>

const emit = defineEmits(['logout']);

const handleLogOut = () => {
    emit('logout');
};

</script>