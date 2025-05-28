import { ref, toValue } from "vue";
import { useFetch } from "./useFetch";
import { useQuasar } from "quasar";

export function useResourceDestroy(resource) {
  const destroyed = ref(null);

  const $q = useQuasar();

  let timeout;

  const setDestroyedTimeout = () => {
    timeout && clearTimeout(timeout);

    setTimeout(() => {
      destroyed.value = null;
    }, 1000);
  };

  const { loading: destroying, execute } = useFetch({
    config: { method: "DELETE" },

    onSuccess() {
      destroyed.value = true;

      $q.notify({
        type: "positive",
        message: "Success",
        caption: "The resource has been deleted successfully.",
        position: "bottom-right",
        timeout: 3000,
      });

      setDestroyedTimeout();
    },

    onError(err) {
      destroyed.value = false;

      $q.notify({
        type: "negative",
        message: "Error",
        caption:
          err.response.data.message ??
          "Something went wrong. Please try again.",
        closeBtn: true,
        timeout: 3000,
      });

      setDestroyedTimeout();
    },
  });

  const destroy = async (id) => {
    const url = toValue(resource) + "/" + toValue(id);

    return await execute({ config: { url } });
  };

  return {
    destroy,
    destroying,
    destroyed,
  };
}
