import { useAuthStore } from "@/stores/auth";

export async function RedirectAuthMiddleware({ to, next }) {

  const authStore = useAuthStore();
  
  await authStore.fetchProfile();

  if (authStore.authenticated) {
    if (authStore.user?.role && authStore.user.role.value === 'admin') {
      return next({ name: 'admin.dashboard' });
    }

    return next({ name: 'dashboard' });
  }

  return next();
}
