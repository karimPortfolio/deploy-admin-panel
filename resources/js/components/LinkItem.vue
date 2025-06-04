<template>
    <q-item
        clickable
        v-ripple
        :exact="item.exact ? true : false"
        :to="item.route"
        active-class="bg-primary-50 text-primary !dark:text-primary border-l-primary"
        class="rounded-md hover:bg-primary-50 hover:text-primary p-0"
    >
        <div class="w-full p-3 flex">
            <q-item-section class="m-0 p-0 w-fit" avatar>
                <q-icon :name="item.icon" />
            </q-item-section>
            <q-item-section class="m-0 p-0 w-fit">
                {{ item.label }}
            </q-item-section>
        </div>
    </q-item>
</template>
<script setup>
defineProps({
    item: {
        type: Object,
        required: true,
    },
});
</script>
