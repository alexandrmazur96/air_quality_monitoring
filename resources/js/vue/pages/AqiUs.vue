<template>
  <AppMenu/>

  <div class="grid grid-nogutter mt-3 p-3">
    <div class="col-12 col-offset-0">
      <h1 class="text-3xl mb-3">United States Air Quality Index</h1>

      <p>
        The U.S. Air Quality Index (AQI) is EPA's tool for communicating about outdoor air quality and
        health.
        The AQI includes six color-coded categories, each corresponding to a range of index values.
        The higher the AQI value, the greater the level of air pollution and the greater the health
        concern.
        The AQI is divided into six categories. Each category corresponds to a different level of health
        concern. Each category also has a specific color. The color makes it easy for people to quickly
        determine whether air quality is reaching unhealthy levels in their communities.
      </p>

      <p class="text-xl mt-3">The table below defines the Air Quality Index scale as defined by the US-EPA
        2016 standard:</p>
      <DataTable :value="usLegendTable" class="mt-3"
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
  name: "AqiUs",
  components: {AppMenu, DataTable, Column},
  data() {
    return {
      usLegendTable: [
        {
          bgColor: '#009966',
          textColor: '#000',
          levelOfConcern: 'Good',
          valuesOfIndex: '0 to 50',
          healthImplications: 'Air quality is considered satisfactory, and air pollution poses little or no risk.'
        },
        {
          bgColor: '#ffde33',
          textColor: '#000',
          levelOfConcern: 'Moderate',
          valuesOfIndex: '51 to 100',
          healthImplications: 'Air quality is acceptable. However, there may be a risk for some people, particularly those who are unusually sensitive to air pollution.'
        },
        {
          bgColor: '#ff9933',
          textColor: '#000',
          levelOfConcern: 'Unhealthy for Sensitive Groups',
          valuesOfIndex: '101 to 150',
          healthImplications: 'Members of sensitive groups may experience health effects. The general public is less likely to be affected.'
        },
        {
          bgColor: '#cc0033',
          textColor: '#000',
          levelOfConcern: 'Unhealthy',
          valuesOfIndex: '151 to 200',
          healthImplications: 'Some members of the general public may experience health effects; members of sensitive groups may experience more serious health effects.'
        },
        {
          bgColor: '#660099',
          textColor: '#fff',
          levelOfConcern: 'Very Unhealthy',
          valuesOfIndex: '201 to 300',
          healthImplications: 'Health alert: The risk of health effects is increased for everyone.'
        },
        {
          bgColor: '#7e0023',
          textColor: '#fff',
          levelOfConcern: 'Hazardous',
          valuesOfIndex: '301 and higher',
          healthImplications: 'Health warning of emergency conditions: everyone is more likely to be affected.'
        },
      ],
    };
  }
}
</script>
