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
            path: "forgot-password",
            name: "auth.forgot.password",
            component: () => import("@/views/auth/ForgotPasswordView.vue"),
        },
        {
            path: "reset-password",
            name: "auth.reset.password",
            component: () => import("@/views/auth/ResetPasswordView.vue"),
        },
    ],
};

