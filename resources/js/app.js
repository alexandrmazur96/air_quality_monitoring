import './bootstrap';

import {createApp} from 'vue/dist/vue.esm-bundler';
import PrimeVue from 'primevue/config';
import Material from '@primevue/themes/material';
import AirQualityMap from "./vue/pages/AirQualityMap.vue";

const appOpts = {
    components: {
        AirQualityMap,
    }
};

const primeVueOpts = {
    theme: {
        preset: Material,
        options: {
            darkModeSelector: 'system',
        }
    }
};

createApp(appOpts)
    .use(PrimeVue, primeVueOpts)
    .mount("#app");
