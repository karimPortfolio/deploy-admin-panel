<template>

  <q-banner class="flex w-full items-center p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-yellow-200">
    <template v-slot:avatar>
      <q-icon name="sym_r_warning"
              class="text-yellow-800 flex-shrink-0 inline text-[21px]" />
    </template>
    <slot name="message" >{{ message }}</slot>
  </q-banner>

</template>
<script setup>

const props = defineProps({
  message: {
    type: String
  }
});

</script>