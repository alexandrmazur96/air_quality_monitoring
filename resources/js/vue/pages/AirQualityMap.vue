<template>
    <AppMenu v-if="!showLegend" @closed="() => menuOpened = false" @opened="() => menuOpened = true"/>
    <SelectButton v-if="!showLegend"
                  optionLabel="label"
                  optionValue="value"
                  dataKey="value"
                  :allow-empty="false"
                  v-model="aqi_index_type"
                  :options="aqi_index_types"
                  class="absolute"
                  style="z-index: 999999; left:50%;transform: translate(-50%, -50%);top:40px"
                  @change="redrawMarkers">
        <template #option="slotProps">
            <p v-tooltip="slotProps.option.tooltip">{{ slotProps.option.label }}</p>
        </template>
    </SelectButton>
    <div id="leafletMap" class="h-screen w-full"></div>

    <Button icon="pi pi-question" rounded @click="() => showLegend = true" v-if="!showLegend && !menuOpened"
            style="position: absolute; z-index: 9999999; right: 50px; top: 50px"/>
    <Dialog v-model:visible="showLegend"
            modal
            header="Air quality indexes explain"
            :style="{ width: '50vw', height: '180vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            class="overflow-auto">
        <Tabs value="0" scrollable>
            <TabList>
                <Tab value="0" as="div" class="flex items-center gap-2">
                    <Avatar image="images/flags/us.svg" shape="circle"/>
                    <span class="font-bold hidden md:block whitespace-nowrap">United States</span>
                </Tab>
                <Tab value="1" as="div" class="flex items-center gap-2">
                    <Avatar image="images/flags/uk.svg" shape="circle"/>
                    <span class="font-bold hidden md:block whitespace-nowrap">United Kingdom</span>
                </Tab>
                <Tab value="2" as="div" class="flex items-center gap-2">
                    <Avatar image="images/flags/eu.svg" shape="circle"/>
                    <span class="font-bold hidden md:block whitespace-nowrap">European Union</span>
                </Tab>
            </TabList>
            <TabPanels>
                <TabPanel value="0" class="m-0">
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
                </TabPanel>
                <TabPanel value="1" as="p" class="m-0">
                    <p>
                        The Daily Air Quality Index (DAQI) tells you about levels of air pollution and provides
                        recommended actions and health advice. The index is numbered 1-10 and divided into four bands,
                        low (1) to very high (10), to provide detail about air pollution levels in a simple way, similar
                        to the sun index or pollen index.

                        You should follow the 3 steps below to use the Daily Air Quality Index.

                        <strong>Step 1</strong>: Determine whether you (or your children) are likely to be at-risk from
                        air pollution.

                        <strong>Step 2</strong>: If you may be at-risk, and are planning strenuous activity outdoors,
                        check the air pollution forecast.

                        <strong>Step 3</strong>: Use the health messages below corresponding to the highest forecast
                        level of pollution as a guide.
                    </p>

                    <p class="text-xl mt-3">The table below defines the Air Quality Index scale as defined by the UK
                        standard:</p>

                    <DataTable :value="ukLegendTable" class="mt-3"
                               :row-style="(row) => {return {'background': row.bgColor, 'color': row.textColor}}">
                        <Column field="levelOfConcern" header="Levels of concern"/>
                        <Column field="valuesOfIndex" header="Values of index"/>
                        <Column field="healthImplications" header="Description of Air Quality"/>
                    </DataTable>
                </TabPanel>
                <TabPanel value="2" as="p" class="m-0">
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
                </TabPanel>
            </TabPanels>
        </Tabs>
    </Dialog>
</template>
<script>
import leaflet from 'leaflet';
import {useGeolocation} from "@vueuse/core";
import {Marker} from "../../types/Marker.js";
import {toRaw} from "vue";
import ukraine from "../../../geojson/ukraine-geoboundaries-adm0.json";
import AppMenu from "../components/AppMenu.vue";
import SelectButton from "primevue/selectbutton";
import Card from "primevue/card";
import Tooltip from "primevue/tooltip";
import Dialog from "primevue/dialog";
import TabPanel from "primevue/tabpanel";
import TabPanels from "primevue/tabpanels";
import Tab from "primevue/tab";
import TabList from "primevue/tablist";
import Tabs from "primevue/tabs";
import Button from "primevue/button";
import Avatar from "primevue/avatar";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import {AirQuality} from "../../types/AirQuality.js";

// Kyiv coordinates
const DEFAULT_LATITUDE = 50.450001;
const DEFAULT_LONGITUDE = 30.523333;

const AQI_INDEX_TYPE_US = 'aqi_us';
const AQI_INDEX_TYPE_UK = 'aqi_uk';
const AQI_INDEX_TYPE_EU = 'aqi_eu';

const {isSupported, coords, error} = useGeolocation()

export default {
    name: "AirQualityMap",
    components: {
        Avatar,
        AppMenu,
        Button,
        SelectButton,
        Card,
        Dialog,
        Tabs,
        TabList,
        Tab,
        TabPanels,
        TabPanel,
        DataTable,
        Column
    },
    directives: {tooltip: Tooltip},
    data() {
        return {
            showLegend: false,
            menuOpened: false,
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

            aqi_index_type: AQI_INDEX_TYPE_US,
            aqi_index_types: [
                {label: 'US', value: AQI_INDEX_TYPE_US},
                {label: 'UK', value: AQI_INDEX_TYPE_UK},
                {label: 'EU', value: AQI_INDEX_TYPE_EU},
            ],
            rawMarkers: [],
            markersOnMap: [],
            map: null,
            border: null,
            userOnMap: null,
            userMarker: new Marker(DEFAULT_LATITUDE, DEFAULT_LONGITUDE, Marker.TYPE_USER),
            geolocationCoords: coords,
            geolocationError: error,
        };
    },
    async mounted() {
        this.map = toRaw(leaflet.map('leafletMap', {zoomControl: false, zoomAnimation:false}));

        const zoomToBordersFn = this.zoomToBorders;
        const zoomToUserFn = this.locateAndZoomIn;

        const zoomToBorderControl = L.Control.extend({
            options: {
                position: 'bottomright'
            },

            onAdd: function (map) {
                const btn = L.DomUtil.create('button', 'leaflet-bar leaflet-control leaflet-control-custom');
                btn.innerHTML = '<i class="pi pi-globe"></i>';
                btn.className = 'p-button p-button-rounded p-button-info w-14 h-14';
                btn.title = 'Zoom to borders';
                btn.onclick = function () {
                    zoomToBordersFn();
                }

                return btn;
            }
        });
        const currentLocationControl = L.Control.extend({
            options: {
                position: 'bottomright'
            },

            onAdd: function (map) {
                const btn = L.DomUtil.create('button', 'leaflet-bar leaflet-control leaflet-control-custom');
                btn.innerHTML = '<i class="pi pi-map-marker"></i>';
                btn.className = 'p-button p-button-rounded p-button-info w-14 h-14';
                btn.title = 'Locate me';
                btn.onclick = function () {
                    zoomToUserFn();
                }

                return btn;
            }
        });

        this.map.addControl(new currentLocationControl());
        this.map.addControl(new zoomToBorderControl());

        leaflet
            .tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 19,
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            })
            .addTo(this.map);

        this.border = leaflet
            .geoJSON(ukraine, {
                style: {
                    opacity: .8,
                    color: "#595cef",
                    fillOpacity: .03,
                    weight: 1,
                }
            })
            .addTo(this.map);

        await this.fillMarkers();

        this.zoomToBorders();
    },
    methods: {
        locateAndZoomIn() {
            this.map.setView([parseFloat(this.geolocationCoords.latitude), parseFloat(this.geolocationCoords.longitude)], 10);
        },
        zoomToBorders() {
            this.map.fitBounds(this.border.getBounds());
        },
        placeUserMarker() {
            if (this.userOnMap) {
                this.map.removeLayer(this.userOnMap);
            }

            this.userOnMap = leaflet
                .marker([this.geolocationCoords.latitude, this.geolocationCoords.longitude], {
                    icon: leaflet.icon({
                        iconUrl: this.userMarker.getIcon(),
                        iconSize: [25, 41],
                        shadowSize: [50, 64],
                        iconAnchor: [12, 41],
                        shadowAnchor: [4, 62],
                    }),
                })
                .addTo(this.map)
                .bindPopup('You are here');
        },
        redrawMarkers() {
            if (!this.aqi_index_type) {
                return;
            }

            this.markersOnMap.forEach(marker => {
                this.map.removeLayer(marker);
            });

            this.markersOnMap = [];
            this.drawMarkers(this.rawMarkers.map((marker) => new Marker(
                marker.latitude,
                marker.longitude,
                Marker.TYPE_AIR_QUALITY,
                new AirQuality(
                    marker.provider,
                    marker.pm10,
                    marker.pm2_5,
                    marker.nh3,
                    marker.o3,
                    marker.no,
                    marker.no2,
                    marker.so2,
                    marker.co,
                    marker.created_at,
                ),
                this.aqi_index_type,
                marker[this.aqi_index_type]
            )));
        },
        drawMarkers(markers) {
            markers.forEach(marker => {
                const leafletMarker = leaflet
                    .marker([marker.latitude, marker.longitude], {
                        icon: leaflet.icon({
                            iconUrl: marker.getIcon(),
                            iconSize: [25, 41],
                            shadowSize: [50, 64],
                            iconAnchor: [12, 41],
                            shadowAnchor: [4, 62],
                        }),
                    })
                    .addTo(this.map);
                this.markersOnMap.push(leafletMarker);
                leafletMarker
                    .bindTooltip(
                        `<div class="marker-tooltip">
<p></p><strong>Provider</strong>: ${marker.airQuality.provider}</p>
<p></p><strong>PM10</strong>: ${marker.airQuality.pm10}</p>
<p></p><strong>PM2.5</strong>: ${marker.airQuality.pm2_5}</p>
<p></p><strong>NH3</strong>: ${marker.airQuality.nh3}</p>
<p></p><strong>O3</strong>: ${marker.airQuality.o3}</p>
<p></p><strong>NO</strong>: ${marker.airQuality.no}</p>
<p></p><strong>NO2</strong>: ${marker.airQuality.no2}</p>
<p></p><strong>SO2</strong>: ${marker.airQuality.so2}</p>
<p></p><strong>CO</strong>: ${marker.airQuality.co}</p>
<p></p><strong>Updated at</strong>: ${marker.airQuality.updated_at}</p>
</div>`,
                        {
                            permanent: false,
                            interactive: false,
                            direction: 'top',
                            className: 'marker-tooltip',
                        }
                    );
            });
        },
        async fillMarkers() {
            await this.loadMarkers().then(markers => {
                this.drawMarkers(markers);
            });
        },
        async loadMarkers() {
            try {
                const response = await axios.get('/current-air-quality-indexes');
                this.rawMarkers = response.data;
                return response.data.map((marker) => new Marker(
                    marker.latitude,
                    marker.longitude,
                    Marker.TYPE_AIR_QUALITY,
                    new AirQuality(
                        marker.provider,
                        marker.pm10,
                        marker.pm2_5,
                        marker.nh3,
                        marker.o3,
                        marker.no,
                        marker.no2,
                        marker.so2,
                        marker.co,
                        marker.created_at,
                    ),
                    this.aqi_index_type,
                    marker[this.aqi_index_type]
                ));
            } catch (error) {
                throw new Error('Error while fetching markers');
            }
        }
    },
    watch: {
        geolocationCoords: {
            handler: function () {
                this.placeUserMarker();
            },
            deep: true
        }
    },
};
</script>
