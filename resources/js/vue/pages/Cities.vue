<template>
    <AppMenu/>

    <div class="grid grid-nogutter p-3">
        <div class="col-12">
            <p class="text-lg md:mt-5 mt-2">The list of supported cities is the following:</p>

            <p v-if="!!errMsg" class="text-red-500 mt-8">{{ errMsg }}</p>
        </div>

        <div v-if="!errMsg" class="md:col-3 md:col-4" v-for="citiesGroup in cities">
            <ul class="list-disc mt-1">
                <li class="ml-5" v-for="city in citiesGroup">{{ city.name }}</li>
            </ul>
        </div>
    </div>
</template>

<script>
import AppMenu from "../components/AppMenu.vue";

export default {
    name: 'Cities',
    components: {AppMenu},
    data() {
        return {
            title: 'Cities',
            cities: [],
            errMsg: '',
        }
    },
    mounted() {
        this.getCities();
    },
    methods: {
        getCities() {
            axios.get('/supported-cities')
                .then(response => {
                    this.cities = response.data;
                })
                .catch(error => {
                    this.errMsg = 'Unable to load supported cities list!';
                });
        }
    }
}
</script>
