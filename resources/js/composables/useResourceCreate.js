import { useQuasar } from "quasar";
import { useFetch } from "./useFetch";
import { mergeDeep } from "./utils/deep-merge";
import { toValue } from "vue";

export function useResourceCreate(resource, { onSuccess, onError, onFinally, config } = {}) {

  const $q = useQuasar();

  config = mergeDeep({ method: "POST" }, config);

  const {
    loading: creating,
    validation,
    execute,
  } = useFetch({
    config,

    onSuccess() {
      $q.notify({
        type: "positive",
        message: "Success",
        caption: "The resource has been created successfully.",
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
          err.response?.data?.message ??
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

  const create = async (payload, config = {}) => {
    config = mergeDeep(
      {
        url: toValue(resource),
        data: payload,
      },
      config
    );

    return await execute({ config });
  };

  return {
    create,
    creating,
    validation,
  };
}
