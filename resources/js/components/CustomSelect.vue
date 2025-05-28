<template>
    <!-- Select -->
    <q-select
      v-model="model"
      v-bind="$attrs"
      :options="filteredOptions"
      :label="label"
      :options-dense="optionsDense"
      :map-options="mapOptions"
      :hide-bottom-space="hideBottomSpace"
      :option-label="optionLabel"
      :option-value="optionValue"
      :dense="dense"
      :clearable
      :loading="loading"
      @filter="onFilter"
      outlined
    >
      <template
        v-for="(_, key) in $slots"
        :key="`slot-${key}`"
        #[key]="slotProps"
      >
        <slot :name="key" v-bind="slotProps ?? {}" />
      </template>
  
      <template v-slot:no-option>
        <q-item>
          <q-item-section class="text-grey"> No results </q-item-section>
        </q-item>
      </template>
    </q-select>
  </template>
  
  <script setup>
  import { computed, onMounted, ref, watch } from "vue";
  import { api } from "@/boot/api";
  
  const props = defineProps({
    label: {
      type: String,
    },
  
    clearable: {
      type: Boolean,
      default: true,
    },
  
    resource: {
      type: String,
      required: true,
    },
  
    method: {
      type: String,
      default: "get",
    },
  
    filters: {
      type: Object,
      default: () => ({}),
    },
  
    optionLabel: {
      type: String,
      default: "name",
    },
  
    optionValue: {
      type: String,
      default: "id",
    },
  
    optionsDense: {
      type: Boolean,
      default: true,
    },
  
    mapOptions: {
      type: Boolean,
      default: true,
    },
  
    dense: {
      type: Boolean,
      default: true,
    },
  
    hideBottomSpace: {
      type: Boolean,
      default: true,
    },
  
    query: {
      type: Object,
      default: () => ({}),
    },
  
    eager: {
      type: Boolean,
      default: false,
    },
  
    filterFn: {
      type: Function,
      default: null,
    },
  });
  
  const model = defineModel();
  
  const options = ref(null);
  
  const loading = ref(false);
  
  const filteredOptions = ref(null);
  
  const filterFn = computed(() => {
    return (
      props.filterFn ||
      ((option, search) => {
        if (!search) return true;
  
        return option[props.optionLabel]
          .toLowerCase()
          .includes(search.toLowerCase());
      })
    );
  });
  
  async function fetchOptions() {
    loading.value = true;
  
    try {
      const params = {
        ...props.query,
        filters: props.filters,
        per_page: 0,
      };
  
      const response = await api.request({
        url: props.resource,
        method: props.method,
        params,
      });
  
      options.value = response.data.data;
    } catch (err) {
      throw err;
    } finally {
      loading.value = false;
    }
  }
  
  async function onFilter(search, done, abort) {
    if (options.value === null) {
      try {
        await fetchOptions();
      } catch (error) {
        abort();
        return;
      }
    }
  
    done(
      () => {
        filteredOptions.value = options.value.filter((option) => {
          return filterFn.value(option, search);
        });
      },
  
      (ref) => {
        if (
          search !== "" &&
          ref.options.length > 0 &&
          ref.getOptionIndex() === -1
        ) {
          ref.moveOptionSelection(1, true); // focus the first selectable option and do not update the input-value
          ref.toggleOption(ref.options[ref.optionIndex], true); // toggle the focused option
        }
      }
    );
  }
  
  onMounted(() => {
    if (props.eager) {
      fetchOptions();
    }
  });
  
  watch([() => props.filters, () => props.resource], () => {
    // Clear selected
    model.value = null;
  
    if (props.eager) {
      fetchOptions();
    } else {
      options.value = null;
    }
  });
  </script>