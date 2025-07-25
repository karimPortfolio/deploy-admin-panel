import { useAuthStore } from "@/stores/auth";

export async function AuthMiddleware({ to, next }) {

  const authStore = useAuthStore();
  
  await authStore.fetchProfile();

  if (!authStore.authenticated) {
    const redirectTo = window.location.href;

    const loginUrl = import.meta.env.VITE_LOGIN_URL;

    window.location.href = loginUrl + "?redirect_to=" + encodeURIComponent(redirectTo);

    return next(false);
  }

  return next();
}
