import { ref, toValue } from "vue";
import { useFetch } from "./useFetch";
import { useQuasar } from "quasar";
import { useI18n } from "vue-i18n";

export function useResourceShow(resource, options = {}) {
    const $q = useQuasar();
    const { t } = useI18n();

    const data = ref(options.data ?? {});

    const { loading, execute } = useFetch({
        onSuccess(resp) {
            data.value = resp.data.data;

            if (options.onSuccess) {
                options.onSuccess(resp);
            }
        },

        onError(err) {
            $q.notify({
                type: "negative",
                message: t('error'),
                caption:
                    err.response.data.message ??
                    t('something_went_wrong_error_msg'),
                closeBtn: true,
                timeout: 3000,
            });
            if (options.onError) {
                options.onError(err);
            }
        },
    });

    const fetch = async (id = null) => {
        return await execute({
            config: {
                url: toValue(resource) + (id ? `/${id}` : ""),
            },
        });
    };

    return {
        loading,
        data,
        fetch,
    };
}
