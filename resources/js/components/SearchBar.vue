<template>
  <div class="flex-1 w-100" :class="customClass" >
      <q-input
        v-model="searchValue"
        dense
        outlined
        :placeholder="`${$t('search')}...`"
        class="ms-auto"
        @keydown.enter="emit('search')"
        hide-bottom-space
      >
        <template v-slot:prepend>
          <q-icon name="search" size="xs" :color="$q.dark.isActive ? 'white' : 'dark'" />
        </template>
      </q-input>
    </div>
</template>

<script setup>

const props = defineProps({
   customClass: {
      type: String,
   }
});

const searchValue = defineModel('searchValue')

const emit = defineEmits(['search'])

</script>