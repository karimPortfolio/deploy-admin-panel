import { useQuasar } from "quasar";
import { useFetch } from "./useFetch";
import { mergeDeep } from "./utils/deep-merge";
import { toValue } from "vue";
import { useI18n } from "vue-i18n";

export function useResourceCreate(resource, { onSuccess, onError, onFinally, config } = {}) {

  const $q = useQuasar();
  const { t } = useI18n();

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
        message: t('success'),
        caption: t('resource_created_success_msg'),
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
        message: t('error'),
        caption:
          err.response?.data?.message ??
          t('something_went_wrong_error_msg'),
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
