<template>
    <AppMenu></AppMenu>
    <div id="leafletMap" class="h-screen w-full"></div>
</template>

<script>
import leaflet from 'leaflet';
import {useGeolocation} from "@vueuse/core";
import {Marker} from "../../types/Marker.js";
import {toRaw} from "vue";
import ukraine from "../../../geojson/ukraine-geoboundaries-adm0.json";
import AppMenu from "../components/AppMenu.vue";

// Kyiv coordinates
const DEFAULT_LATITUDE = 50.450001;
const DEFAULT_LONGITUDE = 30.523333;

const {isSupported, coords, error} = useGeolocation()

export default {
    name: "AirQualityMap",
    components: {AppMenu},
    data() {
        return {
            map: null,
            border: null,
            userOnMap: null,
            userMarker: new Marker(DEFAULT_LATITUDE, DEFAULT_LONGITUDE, Marker.TYPE_USER),
            geolocationCoords: coords,
            geolocationError: error,
        };
    },
    async mounted() {
        this.map = toRaw(leaflet.map('leafletMap', {zoomControl: false}));

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

        this.zoomToBorders();

        await this.fillMarkers();
    },
    methods: {
        locateAndZoomIn() {
            this.map.setView([this.geolocationCoords.latitude, this.geolocationCoords.longitude], 10);
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
        async fillMarkers() {
            await this.loadMarkers().then(markers => {
                markers.forEach(marker => {
                    leaflet
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
                });
            });
        },
        async loadMarkers() {
            try {
                const response = await axios.get('/current-air-quality-indexes');
                return response.data.map((marker) => new Marker(marker.latitude, marker.longitude, Marker.TYPE_AIR_QUALITY, marker.aqi_us));
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
