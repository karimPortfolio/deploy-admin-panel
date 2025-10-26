import { ref, toValue } from "vue";
import { useFetch } from "./useFetch";
import { useQuasar } from "quasar";
import { use } from "react";
import { useI18n } from "vue-i18n";

export function useResourceDestroy(resource) {
  const destroyed = ref(null);

  const $q = useQuasar();
  const { t } = useI18n();

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
        message: t('success'),
        caption: t('resource_deleted_success_msg'),
        position: "bottom-right",
        timeout: 3000,
      });

      setDestroyedTimeout();
    },

    onError(err) {
      destroyed.value = false;

      $q.notify({
        type: "negative",
        message: t('error'),
        caption:
          err.response.data.message ??
          t('something_went_wrong_error_msg'),
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
