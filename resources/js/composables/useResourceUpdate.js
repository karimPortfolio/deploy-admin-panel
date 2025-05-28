import { toValue } from "vue";
import { useFetch } from "./useFetch";
import { mergeDeep } from "./utils/deep-merge";
import { useQuasar } from "quasar";

export function useResourceUpdate(resource, { onSuccess, onError, onFinally, config } = {}) {
  const $q = useQuasar();

  config = mergeDeep({ method: "PUT" }, config);

  const {
    loading: updating,
    validation,
    execute,
  } = useFetch({
    config,

    onSuccess() {
      $q.notify({
        type: "positive",
        message: "Success",
        caption: "The resource has been updated successfully.",
        position: "bottom-right",
        timeout: 3000,
      });

      if (onSuccess) {
        onSuccess();
      }
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

      if (onError) {
        onError(err);
      }
    },

    onFinally() {
      if (onFinally) {
        onFinally();
      }
    }
  });

  const update = async (id, payload, config = {}) => {
    config = mergeDeep(
      {
        url: toValue(resource) + (id ? `/${id}` : ""),
        data: payload,
      },
      config
    );

    return await execute({ config });
  };

  return {
    update,
    updating,
    validation,
  };
}
