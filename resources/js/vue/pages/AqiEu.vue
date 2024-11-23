<template>
    <AppMenu/>

    <div class="grid grid-nogutter mt-3 p-3">
        <div class="col-12 col-offset-0">
            <h1 class="text-3xl mb-3">European Union Air Quality Index</h1>

            <p>
                The European Air Quality Index (EAQI) is a tool designed to provide the public with up-to-date
                information about air quality levels across Europe. It allows individuals to understand the air
                pollution levels in their area and make informed decisions about their health and activities.
                The EAQI uses a color-coded system to categorize air quality, based on the concentration of key
                pollutants such as particulate matter (PM10 and PM2.5), nitrogen dioxide (NO2), ozone (O3), and
                sulfur dioxide (SO2). Each category reflects a specific level of concern for public health and
                is accompanied by advice on actions that can mitigate exposure.
            </p>
            <p class="text-xl mt-3">The table below defines the Air Quality Index scale as defined by the EU
                standard:</p>
            <DataTable :value="euLegendTable" class="mt-3"
                       :row-style="(row) => {return {'background': row.bgColor, 'color': row.textColor}}">
                <Column field="levelOfConcern" header="Levels of concern"/>
                <Column field="valuesOfIndex" header="Values of index"/>
                <Column field="healthImplications" header="Description of Air Quality"/>
            </DataTable>
        </div>
    </div>
</template>

<script>

import Column from "primevue/column";
import DataTable from "primevue/datatable";
import AppMenu from "../components/AppMenu.vue";

export default {
    name: "AqiEu",
    components: {AppMenu, DataTable, Column},
    data() {
        return {
            euLegendTable: [
                {
                    bgColor: 'rgba(80, 240, 230, 1)',
                    textColor: '#000',
                    levelOfConcern: 'Very Good',
                    valuesOfIndex: '1',
                    healthImplications: 'Air quality is very good, posing no health risks.'
                },
                {
                    bgColor: 'rgba(80, 204, 170, 1)',
                    textColor: '#fff',
                    levelOfConcern: 'Good',
                    valuesOfIndex: '2',
                    healthImplications: 'Air quality is good, with very low risk for the population.'
                },
                {
                    bgColor: 'rgba(240, 230, 65, 1)',
                    textColor: '#000',
                    levelOfConcern: 'Moderate',
                    valuesOfIndex: '3',
                    healthImplications: 'Air quality is moderate. Sensitive individuals may experience mild effects.'
                },
                {
                    bgColor: 'rgba(255, 80, 80, 1)',
                    textColor: '#fff',
                    levelOfConcern: 'Poor',
                    valuesOfIndex: '4',
                    healthImplications: 'Air quality is poor. Health risks increase, particularly for sensitive individuals.'
                },
                {
                    bgColor: 'rgba(150, 0, 50, 1)',
                    textColor: '#fff',
                    levelOfConcern: 'Very Poor',
                    valuesOfIndex: '5',
                    healthImplications: 'Air quality is very poor. Health effects are likely for everyone.'
                },
                {
                    bgColor: 'rgba(125, 33, 129, 1)',
                    textColor: '#fff',
                    levelOfConcern: 'Extremely Poor',
                    valuesOfIndex: '6',
                    healthImplications: 'Air quality is extremely poor. Severe health effects are expected for the entire population. Immediate action is recommended.'
                }
            ],
        };
    }
}
</script>
