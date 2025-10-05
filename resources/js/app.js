import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { Dialog, Notify, Quasar } from "quasar";
import VueApexCharts from "vue3-apexcharts";

// Import icon libraries
import "@quasar/extras/roboto-font/roboto-font.css";
import "@quasar/extras/material-icons/material-icons.css";

// Import Quasar css
import "quasar/src/css/index.sass";
import "../scss/quasar.scss";

import { createPinia } from "pinia";

const app = createApp(App);

app.use(router)
app.use(Quasar, {
  plugins: {
    Dialog: Dialog,
    Notify: Notify,
  },
  config: {
    notify: {
      position: "bottom-right",
    },
  },
});

app.use(VueApexCharts);

app.config.errorHandler = (err, vm, info) => {
  console.error("Vue error:", err);
  console.error("Vue info:", info);
};

const pinia = createPinia();
app.use(pinia);

app.mount("#app");

