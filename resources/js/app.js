import './bootstrap';

import {createApp} from 'vue/dist/vue.esm-bundler';
import PrimeVue from 'primevue/config';
import AirQualityMap from "./vue/pages/AirQualityMap.vue";
import Noir from './presets/Noir.js';
import About from "./vue/pages/About.vue";
import Cities from "./vue/pages/Cities.vue";

const appOpts = {
    components: {
        AirQualityMap,
        About,
        Cities
    }
};


const primeVueOpts = {
    theme: {
        preset: Noir,
        options: {
            prefix: 'p',
            darkModeSelector: 'system',
            cssLayer: false
        }
    }
};

createApp(appOpts)
    .use(PrimeVue, primeVueOpts)
    .mount("#app");
