import { defineStore } from "pinia";
import { ref } from "vue";
import { api } from "@/boot/api";
import { useQuasar } from "quasar";

export const useAuthStore = defineStore("auth", () => {
    const user = ref(null);
    const authenticated = ref(false);

    const $q = useQuasar();

    const loadingPromise = ref(null);
    const logoutPromise = ref(null);
    const loginPromise = ref(null);
    const forgetPasswordPromise = ref(null);
    const resetPasswordPromise = ref(null);

    const errorMessages = ref(null);

    const fetchProfile = async () => {
        if (user.value !== null) return;

        if (loadingPromise.value) return loadingPromise.value;

        try {
            loadingPromise.value = await api.get("me");
            user.value = loadingPromise.value.data;
            authenticated.value = true;
        } catch (err) {
            console.log(err);
        } finally {
            loadingPromise.value = null;
        }
    };

    const logout = async () => {
        if (logoutPromise.value) return logoutPromise.value;
        try {
            logoutPromise.value = new Promise((resolve, reject) => {
                api.post("logout")
                    .then((response) => {
                        user.value = null;
                        authenticated.value = false;
                        window.location.href = import.meta.env.VITE_LOGIN_URL;
                        resolve(response);
                    })
                    .catch((error) => {
                        reject(error);
                    });
            });
        } catch (err) {
            console.log(err);
        } finally {
            logoutPromise.value = null;
        }
    };

    const login = async (credentials, redirectTo) => {
        if (loginPromise.value) return loginPromise.value;
        try {
            loginPromise.value = new Promise((resolve, reject) => {
                api.post("login", credentials)
                    .then((response) => {
                        authenticated.value = true;
                        if (redirectTo) window.location.href = redirectTo;
                        window.location.href = "/";
                        resolve(response);
                    })
                    .catch((error) => {
                        if (error.response?.status === 422)
                            errorMessages.value = error.response.data.errors;
                        $q.notify({
                            message: "Error",
                            caption: error.response.data?.message,
                            type: "negative",
                        });
                        reject(error);
                    });
            });
        } catch (err) {
            if (err.response?.status === 422)
                errorMessages.value = err.response.data.errors;
            console.log(err);
        } finally {
            loginPromise.value = null;
        }
    };

    const forgotPassword = async (credentials) => {
        if (forgetPasswordPromise.value) return forgetPasswordPromise.value;
        try {
            forgetPasswordPromise.value = new Promise((resolve, reject) => {
                api.post("forgot-password", credentials)
                    .then((response) => {
                        $q.notify({
                            message: "Success",
                            caption: response.data.message,
                            type: "positive",
                        });
                    })
                    .catch((error) => {
                        if (error.response?.status === 422)
                            errorMessages.value = error.response.data.errors;
                        $q.notify({
                            message: "Error",
                            caption: error.response.data?.message,
                            type: "negative",
                        });
                        reject(error);
                    });
            });
        } catch (err) {
            if (err.response?.status === 422)
                errorMessages.value = err.response.data.errors;
            console.log(err);
        } finally {
            forgetPasswordPromise.value = null;
        }
    };

    const resetPassword = async (credentials, redirectTo) => {
        if (resetPasswordPromise.value) return resetPasswordPromise.value;
        try {
            resetPasswordPromise.value = new Promise((resolve, reject) => {
                api.post("reset-password", credentials)
                    .then((response) => {
                        authenticated.value = true;
                        window.location.href = redirectTo;
                        resolve(response);
                    })
                    .catch((error) => {
                        if (error.response?.status === 422)
                            errorMessages.value = error.response.data.errors;
                        $q.notify({
                            message: "Error",
                            caption: error.response.data?.message,
                            type: "negative",
                        });
                        reject(error);
                    });
            });
        } catch (err) {
            if (err.response?.status === 422)
                errorMessages.value = err.response.data.errors;
            console.log(err);
        } finally {
            resetPasswordPromise.value = null;
        }
    };

    return {
        user,
        authenticated,
        errorMessages,
        fetchProfile,
        logout,
        login,
        forgotPassword,
        resetPassword
    };
});
