<template>
    <q-layout>
        <q-page-container>
            <q-page>
                <div
                    class="min-h-screen flex items-center justify-center bg-gray-100"
                >
                    <div class="text-center px-4 relative">
                        <div class="mb-8 relative">
                            <h1
                                class="text-9xl font-bold text-primary opacity-80 transform hover:scale-105 transition-transform duration-300"
                            >
                                404
                            </h1>
                        </div>

                        <h2
                            class="text-4xl font-bold text-gray-800 tracking-wider mb-4 transform hover:scale-105 transition-transform duration-300"
                        >
                            Oops! Page Not Found
                        </h2>

                        <p
                            class="text-gray-600 mb-8 max-w-md mx-auto leading-relaxed"
                        >
                            The page you are looking for does not exist or has
                            been moved. Please check the URL or return to the
                            home page.
                        </p>

                        <router-link
                            :to="{ name: authStore.homeUrl }"
                            class="inline-flex items-center px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-700 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5"
                        >
                            <i class="fas fa-home mr-2"></i>
                            Return Home
                        </router-link>
                    </div>
                </div>
            </q-page>
        </q-page-container>
    </q-layout>
</template>
<script setup>
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
</script>