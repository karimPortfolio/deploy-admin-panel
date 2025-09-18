import { RouterView } from "vue-router";
import { RedirectAuthMiddleware } from "@/middlewares/RedirectAuthMiddleware";

export default {
    path: "/auth",
    component: RouterView,
    meta: {
        middleware: [RedirectAuthMiddleware],
    },
    children: [
        {
            path: "login",
            name: "auth.login",
            component: () => import("@/views/auth/LoginView.vue"),
        },
        {
            path: "forget-password",
            name: "auth.forget.password",
            component: () => import("@/views/auth/ForgetPasswordView.vue"),
        },
        {
            path: "reset-password",
            name: "auth.reset.password",
            component: () => import("@/views/auth/ResetPasswordView.vue"),
        },
    ],
};
