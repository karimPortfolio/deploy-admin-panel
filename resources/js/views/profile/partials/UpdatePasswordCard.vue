<template>
    <q-card class="mt-4">
        <q-card-section class="pb-0">
            <div class="font-medium">{{ $t('profile.password.label') }}</div>
        </q-card-section>
        <q-separator class="mt-3 mx-4" />
        <q-card-section class="mt-0">
            <q-form>
                <div class="mt-1 grid sm:grid-cols-3">
                    <div>
                        <div class="text-sm">
                            {{ $t('profile.password.current_password') }}
                        </div>
                    </div>
                    <div class="col-span-2 mt-3 sm:mt-0">
                        <q-input
                            type="password"
                            v-model="model.current_password"
                            :error-message="
                                validation.current_password?.[0]
                            "
                            :error="'current_password' in validation"
                            outlined
                            hide-bottom-space
                            dense
                        />
                    </div>
                </div>

                <q-separator class="mt-5" />

                <div class="mt-5 grid sm:grid-cols-3">
                    <div>
                        <div class="text-sm">
                            {{ $t('profile.password.new_password') }}
                        </div>
                    </div>
                    <div class="col-span-2 mt-3 sm:mt-0">
                        <q-input
                            type="password"
                            v-model="model.password"
                            :error-message="validation.password?.[0]"
                            :error="'password' in validation"
                            outlined
                            hide-bottom-space
                            dense
                        />
                    </div>
                </div>

                <q-separator class="mt-5" />

                <div class="mt-5 grid sm:grid-cols-3">
                    <div>
                        <div class="text-sm">
                            {{ $t('profile.password.confirm_password') }}
                        </div>
                    </div>
                    <div class="col-span-2 mt-3 sm:mt-0">
                        <q-input
                            type="password"
                            v-model="model.password_confirmation"
                            :error-message="
                                validation.password_confirmation?.[0]
                            "
                            :error="
                                'password_confirmation' in validation
                            "
                            outlined
                            hide-bottom-space
                            dense
                        />
                    </div>
                </div>

                <q-separator class="mt-5" />

                <div class="mt-6 flex justify-end">
                    <q-btn
                        :label="$t('save')"
                        icon="sym_r_save"
                        color="primary"
                        unelevated
                        :loading="loading"
                        @click="handleDetailsSave"
                    />
                </div>
            </q-form>
        </q-card-section>
    </q-card>
</template>
<script setup>

defineProps({
    loading: {
        type: Boolean
    },
    validation: {
        type: [Object, null]
    }
})

const emit = defineEmits(["save-password"]);
const model = defineModel();

const handleDetailsSave = () => {
    emit("save-password");
}

</script>
