<template>
    <AppMenu></AppMenu>
    <div id="leafletMap" class="h-screen w-full"></div>
</template>

<script>
import leaflet from 'leaflet';
import {useGeolocation} from "@vueuse/core";
import {Marker} from "../../types/Marker.js";
import {shallowRef} from "vue";
import ukraine from "../../../geojson/ukraine-geoboundaries-adm0.json";
import AppMenu from "../components/AppMenu.vue";

export default {
    name: "AirQualityMap",
    components: {AppMenu},
    data() {
        return {
            map: null,
            userMarker: new Marker(50.450001, 30.523333, Marker.TYPE_USER),
        };
    },
    mounted() {
        const {coords} = useGeolocation();
        // console.log(coords.value);
        if (coords.value.latitude !== Number.POSITIVE_INFINITY && coords.value.longitude !== Number.POSITIVE_INFINITY) {
            this.userMarker = new Marker(coords.value.latitude, coords.value.longitude, Marker.TYPE_USER);
        }

        this.map = shallowRef(
            leaflet.map('leafletMap', {zoomControl: false})
                // .setView([this.userMarker.latitude, this.userMarker.longitude], 7)
        );

        leaflet
            .tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 19,
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            })
            .addTo(this.map);

        let border = leaflet
            .geoJSON(ukraine, {
                style: {
                    opacity: .8,
                    color: "#595cef",
                    fillOpacity: .03,
                    weight: 1,
                }
            })
            .addTo(this.map);

        this.map.fitBounds(border.getBounds());

        this.fillMarkers();
    },
    methods: {
        fillMarkers() {
            const markers = [
                // todo: use location from geolocation
                new Marker(50.450001, 30.523333, Marker.TYPE_USER),
                // new Marker(50.450001, 30.523333, Marker.TYPE_USER),
                // new Marker(50.450001, 30.523333, Marker.TYPE_USER),
                // new Marker(50.450001, 30.523333, Marker.TYPE_USER),
                // new Marker(50.450001, 30.523333, Marker.TYPE_USER),
            ];

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
        }
    },
    watch: {},
};
</script>
