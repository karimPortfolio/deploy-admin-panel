<template>
    <q-card >
        <q-card-section class="pb-0">
            <div class="font-medium">Personal informations</div>
        </q-card-section>
        <q-separator class="mt-3 mx-4" />
        <q-card-section class="mt-0">
            <q-form>
                <div class="grid grid-cols-3">
                    <div>
                        <div class="text-sm">Your photo</div>
                        <div class="dark:text-gray-400 text-gray-600 text-xs">
                            These will be displayed on your profile
                        </div>
                    </div>
                    <div class="col-span-2 flex gap-10 items-center">
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
                        <div class="block md:hidden">
                            <q-btn
                                flat
                                icon="sym_r_photo_camera"
                                class="ml-4 rounded-full bg-gray-500 text-white dark:bg-gray-50 dark:text-black relative bottom-8 -right-6"
                                padding="xs xs"
                                @click="openFileSelector"
                            />
                        </div>
                        <div class="hidden md:flex gap-3">
                            <q-btn
                                label="Change photo"
                                color="primary"
                                unelevated
                                @click="openFileSelector"
                            />
                            <q-btn
                                label="Delete photo"
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

                <div class="mt-5 grid grid-cols-3">
                    <div>
                        <div class="text-sm">Name</div>
                    </div>
                    <div class="col-span-2">
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

                <q-separator class="mt-5" />

                <div class="mt-5 grid grid-cols-3">
                    <div>
                        <div class="text-sm">Email Address</div>
                    </div>
                    <div class="col-span-2">
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
                        label="Save"
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
