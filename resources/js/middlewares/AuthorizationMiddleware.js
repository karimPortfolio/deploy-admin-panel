import { useAuthStore } from "@/stores/auth";

export async function AuthorizationMiddleware({ to, next }) {
    const authStore = useAuthStore();

    if (!authStore.authenticated) {
        return next({ name: "auth.login" });
    }

    if (!authStore.user) {
        try {
            await authStore.fetchProfile();
        } catch (err) {
            return next({ name: "auth.login" });
        }
    }

    const requiredRole =
        to.meta.role || to.matched.find((r) => r.meta.role)?.meta.role;

    if (requiredRole && authStore.user.role?.value !== requiredRole) {
        return next({ name: "forbidden" });
    }

    return next();
}
