<template>
    <Menubar :model="items">
        <template #start>
            <Image src="images/logo.png" alt="Air Quality logotype that is link to home page" class="mr-3" imageClass="w-2rem h-2rem" />
        </template>

        <template #item="{item}">
            <a :href="item.link" class="p-1"><span :class="item.icon" /> {{ item.label }}</a>
        </template>
    </Menubar>
</template>

<style>
.p-menubar-item {
    padding: 0.25rem;
}
</style>

<script>
import Menubar from "primevue/menubar";
import Image from "primevue/image";

export default {
    name: 'AppMenu',
    components: {
        Menubar,
        Image
    },
    data() {
        return {
            items: [
                {
                    label: 'Map',
                    icon: 'pi pi-map',
                    link: '/'
                },
                {
                    label: 'About',
                    icon: 'pi pi-info-circle',
                    link: '/about'
                },
                {
                    label: 'Supported cities',
                    icon: 'pi pi-warehouse',
                    link: '/supported-cities'
                }
            ]
        }
    }
}
</script>
