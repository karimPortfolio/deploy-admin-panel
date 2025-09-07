import { createRouter, createWebHistory, RouterView } from "vue-router";
import { AuthMiddleware } from "@/middlewares/AuthMiddleware";
import { AuthorizationMiddleware } from "../middlewares/AuthorizationMiddleware";
import { middlewarePipeline } from "../middlewares/middlewarePipeline";
import adminRoutes from "./admin-routes";
import authRoutes from "./auth-routes";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            // BASE ROUTES
            path: "/",
            name: "index",
            component: () => import("../views/indexView.vue"),
            meta: {
                middleware: [AuthMiddleware, AuthorizationMiddleware],
            },
            children: [
                // USER ROUTES
                {
                    path: "",
                    name: "dashboard",
                    component: () => import("../views/dashboard/IndexView.vue"),
                    meta: { role: "user" },
                },
                {
                    path: "servers",
                    component: RouterView,
                    meta: { role: "user" },
                    children: [
                        {
                            path: "",
                            name: "servers.index",
                            component: () =>
                                import("../views/servers/IndexView.vue"),
                        },
                        {
                            path: ":id",
                            name: "servers.show",
                            component: () =>
                                import("../views/servers/ShowDetailsView.vue"),
                        },
                    ],
                },
                {
                    path: "security-groups",
                    name: "security-groups",
                    meta: { role: "user" },
                    component: () =>
                        import("../views/security-groups/IndexView.vue"),
                },
                {
                    path: "ssh-keys",
                    name: "ssh-keys",
                    meta: { role: "user" },
                    component: () => import("../views/ssh-keys/IndexView.vue"),
                },
                {
                    path: "profile",
                    name: "profile",
                    meta: { role: "user" },
                    component: () => import("../views/profile/IndexView.vue"),
                },
                {
                    path: "settings",
                    name: "settings",
                    meta: { role: "user" },
                    component: () => import("../views/settings/IndexView.vue"),
                },

                // ADMIN ROUTES
                adminRoutes
            ],
        },

        // AUTH ROUTES
        authRoutes,

        // ERROR PAGES ROUTES
        {
            path: "/forbidden",
            name: "forbidden",
            component: () => import("@/views/errors/ForbiddenView.vue"),
        },
        {
            path: "/:catchAll(.*)",
            name: "not-found",
            component: () => import("@/views/errors/NotFoundView.vue"),
        },
    ],
});

router.beforeEach(async (to, from, next) => {
    if (!to.meta.middleware) {
        return next();
    }

    // Get all middlewares from matched routes
    const middleware = to.matched
        .map((route) => route.meta.middleware)
        .filter(Boolean)
        .flat();

    const context = {
        to,
        from,
        next,
    };

    return await middleware[0]({
        ...context,
        next: middlewarePipeline(context, middleware, 1),
    });
});

export default router;
