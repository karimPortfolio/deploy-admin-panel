import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { fileURLToPath, URL } from "node:url";
import vue from "@vitejs/plugin-vue";
import { quasar, transformAssetUrls } from "@quasar/vite-plugin";
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        vue({
            template: { transformAssetUrls },
        }),
        laravel({
            input: ['resources/scss/quasar-variables.scss', 'resources/js/app.js'],
            refresh: true,
        }),
        quasar({
            autoImportComponentCase: "combined",
            sassVariables: fileURLToPath(new URL("./resources/scss/quasar-variables.scss", import.meta.url)),
        }),
        tailwindcss()
    ],

    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            "@": fileURLToPath(new URL("./resources/js", import.meta.url)),
        },
    },

    server: {
        port: 4000,
        cors: {
          origin: 'http://deploy-admin-panel.test',
          credentials: true,
        },
    },
});
