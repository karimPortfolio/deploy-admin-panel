<template>
    <q-card >
        <q-card-section class="pb-0">
            <div class="font-medium">{{ $t('profile.personal_info') }}</div>
        </q-card-section>
        <q-separator class="mt-3 mx-4" />
        <q-card-section class="mt-0">
            <q-form>
                <div class="grid sm:grid-cols-3">
                    <div>
                        <div class="text-sm">{{ $t('profile.photo.label') }}</div>
                        <div class="dark:text-gray-400 text-gray-600 text-xs">
                            {{ $t('profile.photo.subtitle') }}
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-0 col-span-2 flex gap-5 sm:gap-10 items-center">
                        <q-avatar v-if="uploadedPhoto" class="w-20 h-20">
                            <q-img
                                :src="uploadedPhoto"
                            />
                        </q-avatar>
                        <q-avatar v-else class="w-20 h-20">
                            <q-img
                                :src="
                                    model?.photo ??
                                    '/src/img/avatar.png'
                                "
                            />
                        </q-avatar>
                        <div class="flex gap-3">
                            <q-btn
                                :label="$q.screen.lt.sm ? $t('profile.photo.change') : $t('profile.photo.change_photo')"
                                color="primary"
                                unelevated
                                @click="openFileSelector"
                            />
                            <q-btn
                                :label="$q.screen.lt.sm ? $t('profile.photo.remove') : $t('profile.photo.remove_photo')"
                                color="red"
                                outline
                                unelevated
                                :disable="!uploadedPhoto"
                                @click="resetSelectedImage"
                            />
                        </div>
                    </div>
                </div>

                <q-separator class="mt-4" />

                <div class="mt-5 grid sm:grid-cols-3">
                    <div>
                        <div class="text-sm">{{ $t('name') }}</div>
                    </div>
                    <div class="col-span-2 mt-3 sm:mt-0">
                        <q-input
                            v-model="model.name"
                            :error-message="validation.name?.[0]"
                            :error="'name' in validation"
                            outlined
                            hide-bottom-space
                            dense
                        />
                    </div>
                </div>

                <q-separator class="mt-4" />

                <div class="mt-5 grid sm:grid-cols-3">
                    <div>
                        <div class="text-sm">{{ $t('users.company_name') }}</div>
                    </div>
                    <div class="col-span-2 mt-3 sm:mt-0">
                        <q-input
                            v-model="model.company_name"
                            :error-message="validation.company_name?.[0]"
                            :error="'company_name' in validation"
                            outlined
                            hide-bottom-space
                            dense
                        />
                    </div>
                </div>

                <q-separator class="mt-5" />

                <div class="mt-5 grid sm:grid-cols-3">
                    <div>
                        <div class="text-sm">{{ $t('users.email') }}</div>
                    </div>
                    <div class="col-span-2 mt-3 sm:mt-0">
                        <q-input
                            v-model="model.email"
                            :error-message="validation.email?.[0]"
                            :error="'email' in validation"
                            outlined
                            hide-bottom-space
                            dense
                        />
                    </div>
                </div>

                <q-separator class="mt-5" />

                <div class="mt-6 flex justify-end">
                    <q-btn
                        :label=" $t('save')"
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
    },
    uploadedPhoto: {
        type: [String, null]
    }
})

const emit = defineEmits(["save-details", "open-file-selector", "reset-image"]);
const model = defineModel();

const handleDetailsSave = () => {
    emit("save-details");
}

const openFileSelector = () => {
    emit("open-file-selector");
}

const resetSelectedImage = () => {
    emit("reset-image");
}

</script>
