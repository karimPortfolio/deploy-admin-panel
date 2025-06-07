import { createRouter, createWebHistory } from "vue-router";
import { AuthMiddleware } from "@/middlewares/AuthMiddleware";
import { RedirectAuthMiddleware } from "@/middlewares/RedirectAuthMiddleware";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            name: "index",
            component: () => import("../views/indexView.vue"),
            meta: {
                middleware: [AuthMiddleware],
            },
            children: [
                {
                    path: "",
                    name: "dashboard",
                    component: () =>
                        import("../views/dashboard/IndexView.vue"),
                },
                {
                    path: "servers",
                    name: "servers",
                    component: () =>
                        import("../views/servers/IndexView.vue"),
                },
                {
                    path: "security-groups",
                    name: "security-groups",
                    component: () =>
                        import("../views/security-groups/IndexView.vue"),
                },
                {
                    path: "ssh-keys",
                    name: "ssh-keys",
                    component: () =>
                        import("../views/ssh-keys/IndexView.vue"),
                },
            ],
        },
        {
            path: "/auth/login",
            name: "auth.login",
            meta: {
                middleware: [RedirectAuthMiddleware],
            },
            component: () => import("@/views/auth/LoginView.vue"),
        },
        {
            path: "/auth/forget-password",
            name: "auth.forget.password",
            meta: {
                middleware: [RedirectAuthMiddleware],
            },
            component: () => import("@/views/auth/ForgetPasswordView.vue"),
        },
        {
            path: "/auth/reset-password",
            name: "auth.reset.password",
            meta: {
                middleware: [RedirectAuthMiddleware],
            },
            component: () => import("@/views/auth/ResetPasswordView.vue"),
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
        //   store  | You can also pass store as an argument
    };

    return await middleware[0]({
        ...context,
        // next: middlewarePipeline(context, middleware, 1),
    });
});

export default router;