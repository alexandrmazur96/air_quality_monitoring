<template>
  <AppMenu/>

  <div class="grid grid-nogutter mt-3 p-3">
    <div class="col-12 col-offset-0">
      <h1 class="text-3xl mb-3">United Kingdom Air Quality Index</h1>
      <p class="mb-3">
        The Daily Air Quality Index (DAQI) tells you about levels of air pollution and provides
        recommended actions and health advice. The index is numbered 1-10 and divided into four bands,
        low (1) to very high (10), to provide detail about air pollution levels in a simple way, similar
        to the sun index or pollen index.

        You should follow the 3 steps below to use the Daily Air Quality Index.

        <ul class="list-none">
          <li><strong>Step 1</strong>: Determine whether you (or your children) are likely to be at-risk from
            air pollution.
          </li>

          <li><strong>Step 2</strong>: If you may be at-risk, and are planning strenuous activity outdoors,
            check the air pollution forecast.
          </li>

          <li><strong>Step 3</strong>: Use the health messages below corresponding to the highest forecast
            level of pollution as a guide.
          </li>
        </ul>
      </p>

      <p class="text-xl mt-3">The table below defines the Air Quality Index scale as defined by the UK
        standard:</p>

      <DataTable :value="ukLegendTable" class="mt-3"
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
  name: "AqiUk",
  components: {AppMenu, DataTable, Column},
  data() {
    return {
      ukLegendTable: [
        {
          bgColor: '#090',
          textColor: '#000',
          levelOfConcern: 'Low',
          valuesOfIndex: '1-3',
          healthImplications: 'Enjoy your usual outdoor activities.'
        },
        {
          bgColor: '#f90',
          textColor: '#000',
          levelOfConcern: 'Moderate',
          valuesOfIndex: '4-6',
          healthImplications: 'Adults and children with lung problems, and adults with heart problems, who experience symptoms, should consider reducing strenuous physical activity, particularly outdoors.'
        },
        {
          bgColor: 'red',
          textColor: '#000',
          levelOfConcern: 'High',
          valuesOfIndex: '7-9',
          healthImplications: 'Adults and children with lung problems, and adults with heart problems, should reduce strenuous physical exertion, particularly outdoors, and particularly if they experience symptoms. People with asthma may find they need to use their reliever inhaler more often. Older people should also reduce physical exertion.'
        },
        {
          bgColor: '#909',
          textColor: '#fff',
          levelOfConcern: 'Very High',
          valuesOfIndex: '10',
          healthImplications: 'Adults and children with lung problems, adults with heart problems, and older people, should avoid strenuous physical activity. People with asthma may find they need to use their reliever inhaler more often.'
        },
      ],
    };
  }
}
</script>
