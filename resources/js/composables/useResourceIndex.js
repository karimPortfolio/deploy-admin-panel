import { useQuasar } from "quasar";
import { reactive, ref, toRaw, toValue, watchEffect } from "vue";
import { useFetch } from "./useFetch";
import qs from "qs";

export function useResourceIndex(
  resource,
  initOptions = {},
  { onSuccess, onError, onFinally, config } = {}
) {
  const $q = useQuasar();

  const options = reactive({
    search: null,
    pagination: {
      sortBy: null,
      descending: false,
      page: 1,
      rowsNumber: 0,
      rowsPerPage: initOptions.pagination?.rowsPerPage ?? 10,
      from: 0,
      to: 0,
    },

    filters: {},
    meta: {},
  });

  watchEffect(() => {
    const opts = toValue(initOptions ?? {});

    options.pagination.rowsPerPage =
      opts.pagination?.rowsPerPage ?? options.pagination.rowsPerPage;

    options.filters = opts.filters ?? options.filters ?? {};

  });

  const data = ref(null);

  const { loading, initialLoading, execute } = useFetch({
    config,

    onSuccess(resp) {
      data.value = resp.data.data;

      if (onSuccess) onSuccess(resp);

      if (!resp.data.meta) return;

      // Extract pagination
      options.pagination.rowsPerPage = resp.data.meta.per_page;
      options.pagination.page = resp.data.meta.current_page;
      options.pagination.from = resp.data.meta.from;
      options.pagination.to = resp.data.meta.to;
      options.pagination.rowsNumber = resp.data.meta.total;

      // Extract meta
      options.meta = resp.data.meta;
    },

    onError(err) {
      $q.notify({
        type: "negative",
        message: "Error",
        caption:
          err.response.data.message ??
          "Something went wrong. Please try again.",
        closeBtn: true,
        timeout: 3000,
      });

      if (onError) onError(err);
    },

    onFinally() {
      if (onFinally) onFinally();
    },
  });

  const getSortBy = (pagination) => {
    if (!pagination.sortBy) return null;

    return `${pagination.descending ? "-" : ""}${pagination.sortBy}`;
  };

  async function fetch({ pagination, filters, filter } = options) {
    filter ??= options.search;
    pagination ??= options.pagination;
    filters ??= options.filters;

    options.search = filter;

    const url = toValue(resource);

    const params = qs.stringify({
      page: pagination.page,
      search: options.search,
      per_page: pagination.rowsPerPage,
      sort: getSortBy(pagination),
      filter: filters,
    });

    return await execute({
      config: {
        url: `${url}?${params}`,
      },
    }).then((resp) => {
      options.filters = filters;

      if (pagination.sortBy) {
        options.pagination.sortBy = pagination.sortBy;
        options.pagination.descending = pagination.descending;
      } else {
        options.pagination.sortBy = null;
        options.pagination.descending = false;
      }

      return resp;
    });
  }

  const onRequest = fetch;

  return {
    options,
    loading,
    initialLoading,
    data,
    onRequest,
    fetch,
  };
}
