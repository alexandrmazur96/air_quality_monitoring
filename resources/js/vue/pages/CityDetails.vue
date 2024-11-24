<template>
    <AppMenu />

    <div class="grid grid-nogutter p-3">
        <div class="col-12">
            <p class="text-lg md:mt-5 mt-2">{{ city.name }}, Ukraine ({{ city.latitude }}, {{ city.longitude }})</p>
        </div>
        <div class="col-12 md:col-6 p-3">
            <p class="text-lg md:mt-5 mt-2">Air quality indexes</p>
            <DataTable :value="aqis">
                <template #empty>No air quality indexes information available!</template>
                <Column field="type" header="AQI" />
                <Column field="value" header="Value" />
                <Column field="description" header="Explanation" />
            </DataTable>
        </div>
        <div class="col-12 md:col-6 p-3">
            <p class="text-lg md:mt-5 mt-2">Pollutants details</p>
            <DataTable :value="measurements">
                <template #empty>No measurements information available!</template>
                <Column field="pollutant" header="Pollutant" />
                <Column field="value" header="Value" />
            </DataTable>
        </div>
    </div>
</template>

<script>
import AppMenu from "../components/AppMenu.vue";
import Column from "primevue/column";
import DataTable from "primevue/datatable";

export default {
    name: 'CityDetails',
    props: ['city', 'measurements', 'aqis'],
    components: {AppMenu, DataTable, Column},
    data() {
        return {
        };
    },
};
</script>
