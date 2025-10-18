<template>
    <q-btn padding="sm" size="sm" unelevated>
        <span class="relative">
            <!-- <q-icon name="far fa-bell" /> -->
            <q-icon name="sym_r_notifications" />
            <span
                v-if="unreceivedNotificationsCount > 0"
                class="absolute top-0 right-0 w-2 h-2 bg-primary ring-2 ring-gray-50 dark:ring-slate-800 rounded-full"
            ></span>
        </span>
        <keep-alive>
            <q-menu
                :offset="[0, 6]"
                class="w-96 max-h-[500px] rounded-lg shadow-lg border dark:border-gray-700"
                transition-show="jump-down"
                transition-hide="jump-up"
            >
                <div
                    class="flex justify-between items-center flex-nowrap py-3 px-4"
                >
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-medium">{{ $t('notifications.title') }}</h3>
                        <q-badge
                            v-if="unreadNotificationsCount > 0"
                            class="bg-blue-100 rounded-full py-1 text-blue-500 maw-h-0"
                            :label="unreadNotificationsCount"
                        />
                    </div>
                    <div>
                        <a
                            role="button"
                            class="text-primary text-sm font-medium"
                            @click="markAllAsRead()"
                            :title="$t('notifications.mark_all_as_read')"
                        >
                            <q-icon
                                name="sym_r_done_all"
                                class="text-sm me-1"
                            />
                            {{ truncate($t('notifications.mark_all_as_read'), 12) }}
                        </a>
                    </div>
                </div>
                <q-separator />
                <q-list dense class="">
                    <q-infinite-scroll @load="onLoad" :offset="250">
                        <div v-if="notifications.length > 0">
                            <q-item
                                v-for="(notification, index) in notifications"
                                :key="`notification-${index}`"
                                :clickable="notification.data.link !== null"
                                class="py-4 border-b group w-full"
                                target="_blank"
                                :href="notification.data.link"
                                @click="markNotificationAsRead(notification.id)"
                                :class="
                                    notification.read_at == null
                                        ? 'bg-slate-100 dark:bg-slate-700'
                                        : ''
                                "
                            >
                                <q-item-section class="w-full">
                                    <div class="flex flex-nowrap w-full gap-3">
                                        <q-avatar>
                                            <div class="bg-gray-50 text-dark p-3">
                                                <q-icon
                                                    name="sym_r_settings"
                                                    size="27px"
                                                />
                                            </div>
                                            <!-- <q-badge
                                                v-if="
                                                    notification.read_at == null
                                                "
                                                floating
                                                text-color="blue"
                                                label="New"
                                                rounded
                                                class="absolute-top-right text-[8px] -top-[10px] -left-[10px] w-fit"
                                            /> -->
                                        </q-avatar>
                                        <div class="w-full">
                                            <q-item-label class="font-medium">{{
                                                notification.data.title
                                            }}</q-item-label>
                                            <q-item-label caption>{{
                                                notification.data.body
                                            }}</q-item-label>
                                            <div
                                                class="flex justify-between items-center w-full gap-2 mt-1"
                                            >
                                                <q-item-label caption>{{
                                                    notification.created_at
                                                }}</q-item-label>
                                                <div
                                                    class="group-hover:opacity-100 opacity-0 flex items-center gap-2"
                                                >
                                                    <button
                                                        class="border-0 bg-transparent hover:text-blue-500 transition-all ease-in-out duration-150 text-xs"
                                                        @click.self="
                                                            confirmDelete(
                                                                notification.id
                                                            )
                                                        "
                                                    >
                                                        <q-icon
                                                            name="sym_r_done_all"
                                                            size="xs"
                                                        />
                                                        <q-tooltip
                                                            anchor="top middle"
                                                            self="center middle"
                                                        >
                                                            {{ $t('notifications.mark_as_read') }}
                                                        </q-tooltip>
                                                    </button>
                                                    <button
                                                        class="border-0 bg-transparent hover:text-red-500 transition-all ease-in-out duration-150 text-xs"
                                                        @click.self="
                                                            confirmDelete(
                                                                notification.id
                                                            )
                                                        "
                                                    >
                                                        <q-icon
                                                            name="sym_r_delete"
                                                            size="xs"
                                                        />
                                                        <q-tooltip
                                                            anchor="top middle"
                                                            self="center middle"
                                                        >
                                                            {{ $t('notifications.delete') }}
                                                        </q-tooltip>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </q-item-section>
                            </q-item>
                        </div>
                        <div
                            v-else-if="!loading && notifications.length == 0"
                            class="flex justify-center items-center"
                        >
                            <q-img
                                src="/src/img/no-notifications.png"
                                class="w-3/5"
                            />
                            <div class="text-gray-600 dark:text-gray-400 text-lg mt-2 mb-5"
                                :title="$t('notifications.no_notifications')" 
                            >
                                {{ truncate($t('notifications.no_notifications'), 40) }}
                            </div>
                        </div>
                        <template v-slot:loading>
                            <div class="row justify-center q-my-md">
                                <q-spinner class="text-blue-500" size="30px" />
                            </div>
                        </template>
                    </q-infinite-scroll>
                </q-list>
            </q-menu>
        </keep-alive>
    </q-btn>
</template>
<script setup>
import { useFetch } from "@/composables/useFetch";
import { useQuasar } from "quasar";
import { useResourceIndex } from "@/composables/useResourceIndex";
import { useTextTruncate } from "@/composables/useTextTruncate";
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { useI18n } from "vue-i18n";

const showConfirmDeleteDialog = ref(false);

const $q = useQuasar();
const { truncate } = useTextTruncate();
const { t } = useI18n();

const notifications = ref([]);

const {
    data: pageNotifications,
    options,
    fetch,
    loading,
} = useResourceIndex("notifications", {
    onSuccess() {
        appendNotifications();
    },
});

const unreadNotificationsCount = computed(() => {
    return notifications.value.filter(
        (notification) => notification.read_at === null
    ).length;
});

function appendNotifications() {
    notifications.value = [...notifications.value, ...pageNotifications.value];
}

const onLoad = async (page, done) => {
    if (unreceivedNotificationsCount.value !== 0) {
        unreceivedNotificationsCount.value = 0;
    }

    if (
        notifications.value.length === options.pagination.rowsNumber &&
        notifications.value.length > 0
    ) {
        done(true);
        return;
    }

    options.pagination.page = page;

    await fetch();

    appendNotifications();

    if (page > options.pagination.rowsNumber / options.pagination.rowsPerPage) {
        done(true);
        return;
    }

    done();
};

const { loading: markAllAsReadLoading, execute: markAllAsRead } = useFetch({
    config: {
        url: "notifications/mark-all-as-read",
        method: "PUT",
    },

    async onSuccess() {
        await fetch();
        notifications.value = [];
        appendNotifications();
        $q.notify({
            type: "positive",
            message: t('success'),
            caption: t('notifications.marked_all_as_read_success'),
            position: "bottom-right",
            timeout: 3000,
        });
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
    },
});

const markingAsRead = ref({});

const { execute: markAsRead } = useFetch({
    config: {
        method: "PUT",
    },

    async onSuccess() {
        await fetch();
        notifications.value = [];
        appendNotifications();
    },

    onError() {
        $q.notify({
            message: t('error'),
            caption: t('something_went_wrong_error_msg'),
            position: "bottom-right",
            closeable: true,
            type: "error",
        });
    },
});

const unreceivedNotificationsCount = ref(null);

const { execute: fetchUnreceivedNotification } = useFetch({
    config: {
        method: "GET",
    },

    onSuccess(response) {
        unreceivedNotificationsCount.value =
            response.data.data.unreceived_notifications_count;
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
    },
});

const destroying = ref({});

const { execute: destroy } = useFetch({
    config: {
        method: "DELETE",
    },

    onSuccess() {
        $q.notify({
            type: "positive",
            message: t('success'),
            caption: t('notifications.deleted_success'),
            position: "bottom-right",
            timeout: 3000,
        });
        fetch();
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
    },
});

const markNotificationAsRead = async (id) => {
    markingAsRead.value = { [id]: true };

    await markAsRead({
        config: {
            url: `notifications/${id}/mark-as-read`,
        },
    });

    delete markingAsRead.value[id];
};

const fetchUnreceivedNotificationCount = async () => {
    await fetchUnreceivedNotification({
        config: {
            url: `notifications/unreceived-notifications`,
        },
    });
};

const itemToDelete = ref(null);

const confirmDelete = async (item) => {
    $q.dialog({
        component: DeleteDialog,

        progress: true,

        componentProps: {
            resource: "notifications",
            name: item.name,
            id: item.id,
        },
    })
        .onOk(async () => {
            options.pagination.page = 1;

            await fetch();
            notifications.value = [];
            appendNotifications();
        })
        .onCancel(() => {
            // Do nothing
        });
};

const handleDelete = async () => {
    destroying.value = { [itemToDelete.value]: true };

    await destroy({
        config: {
            url: `notifications/${itemToDelete.value}`,
        },
    });

    setTimeout(() => {
        showConfirmDeleteDialog.value = false;
    }, 1000);

    delete destroying.value[itemToDelete.value];
};

onMounted(() => {
    // fetch();
    fetchUnreceivedNotificationCount();
    startInterval();
});

let interval = null;

const startInterval = () => {
    interval = setInterval(() => {
        // fetch();
        fetchUnreceivedNotificationCount();
    }, 20000);
};

const stopInterval = () => {
    clearInterval(interval);
};

onBeforeUnmount(() => {
    stopInterval();
});
</script>
