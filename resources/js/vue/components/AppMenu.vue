<template>
    <Menubar :model="items">
        <template #start>
            <a href="/"><Image src="images/logo.png" alt="Air Quality logotype that is link to home page" class="mr-3" imageClass="w-2rem h-2rem" /></a>
        </template>

        <template #item="{item}">
            <a :href="item.link" class="p-1"><span v-if="!item.asAvatar" :class="item.icon" /> <Avatar :image="item.img" v-if="item.asAvatar" class="mr-2" shape="circle" /> {{ item.label }}</a>
        </template>
    </Menubar>
</template>

<style>
.p-menubar-item {
    padding: 0.5rem;
    cursor: pointer;
}
.p-menubar {
    border:initial;
}
</style>

<script>
import Menubar from "primevue/menubar";
import Image from "primevue/image";
import Avatar from "primevue/avatar";

export default {
    name: 'AppMenu',
    components: {
        Menubar,
        Image,
        Avatar
    },
    data() {
        return {
            items: [
                {
                    label: 'Map',
                    icon: 'pi pi-map',
                    link: '/',
                    asAvatar: false,
                },
                {
                    label: 'About',
                    icon: 'pi pi-info-circle',
                    link: '/about',
                    asAvatar: false,
                },
                {
                    label: 'Supported cities',
                    icon: 'pi pi-warehouse',
                    link: '/supported-cities',
                    asAvatar: false,
                },
                {
                    label: 'Air Quality Indexes',
                    icon: 'pi pi-sort-down',
                    items: [
                        {
                            label: 'United States AQI',
                            icon: '',
                            link: '/aqi-us',
                            asAvatar: true,
                            img: 'images/flags/us.svg'
                        },
                        {
                            label: 'United Kingdom AQI',
                            icon: '',
                            link: '/aqi-uk',
                            asAvatar: true,
                            img: 'images/flags/uk.svg'
                        },
                        {
                            label: 'European Union AQI',
                            icon: '',
                            link: '/aqi-eu',
                            asAvatar: true,
                            img: 'images/flags/eu.svg'
                        }
                    ]
                }
            ]
        }
    }
}
</script>
