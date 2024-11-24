import './bootstrap';

import {createApp} from 'vue/dist/vue.esm-bundler';
import PrimeVue from 'primevue/config';
import Material from '@primevue/themes/material';

import AirQualityMap from "./vue/pages/AirQualityMap.vue";
import Cities from "./vue/pages/Cities.vue";
import CityDetails from "./vue/pages/CityDetails.vue";
import About from "./vue/pages/About.vue";
import AqiUs from "./vue/pages/AqiUs.vue";
import AqiUk from "./vue/pages/AqiUk.vue";
import AqiEu from "./vue/pages/AqiEu.vue";

const appOpts = {
    components: {
        AirQualityMap,
        About,
        Cities,
        CityDetails,
        AqiUs,
        AqiUk,
        AqiEu,
    }
};


const primeVueOpts = {
    theme: {
        preset: Material,
    }
};

createApp(appOpts)
    .use(PrimeVue, primeVueOpts)
    .mount("#app");
