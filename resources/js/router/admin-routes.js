import { RouterView } from "vue-router";

export default {
    path: "admin",
    component: RouterView,
    meta: {
        role: "admin",
    },
    children: [
        {
            path: "dashboard",
            name: "admin.dashboard",
            component: () => import("../views/admin/dashboard/IndexView.vue"),
        },
        {
            path: "users",
            name: "admin.users",
            component: () => import("../views/admin/users/IndexView.vue"),
        },
        {
            path: "servers",
            component: RouterView,
            children: [
                {
                    path: "",
                    name: "admin.servers.index",
                    component: () =>
                        import("../views/admin/servers/IndexView.vue"),
                },
                {
                    path: ":id",
                    name: "admin.servers.show",
                    component: () =>
                        import("../views/admin/servers/ShowDetailsView.vue"),
                },
            ],
        },
    ],
};
