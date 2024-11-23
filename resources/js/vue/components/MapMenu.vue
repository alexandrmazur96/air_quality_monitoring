<template>
    <Drawer v-model:visible="visible" @hide="drawerClosed" @show="">
        <template #container="{ closeCallback }">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between px-6 pt-4 shrink-0">
                    <span class="inline-flex items-center gap-2">
                        <span class="font-semibold text-2xl text-primary">Air Quality</span>
                    </span>
                    <span>
                        <Button type="button" @click="closeCallback" icon="pi pi-times" rounded outlined></Button>
                    </span>
                </div>
                <hr class="mt-4 mb-4 mx-4 border-t border-0 border-surface-200 dark:border-surface-700" />

                <div class="overflow-y-auto">
                    <ul class="list-none p-0 m-0 overflow-hidden">
                        <li>
                            <a class="flex items-center cursor-pointer pl-4 p-3 rounded text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors p-ripple"
                               href="/">
                                <i class="mr-2 pi pi-map"></i>
                                <span class="font-medium">Map</span>
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center cursor-pointer pl-4 p-3 rounded text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors p-ripple"
                               href="/about">
                                <i class="mr-2 pi pi-info-circle"></i>
                                <span class="font-medium">About</span>
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center cursor-pointer pl-4 p-3 rounded text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors p-ripple"
                               href="/supported-cities">
                                <i class="mr-2 pi pi-warehouse"></i>
                                <span class="font-medium">Supported cities</span>
                            </a>
                        </li>
                        <li>
                            <Accordion :value="0">
                                <AccordionPanel :value="0">
                                    <AccordionHeader>Air quality indexes</AccordionHeader>
                                    <AccordionContent>
                                        <a class="flex items-center cursor-pointer pl-4 p-3 rounded text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors p-ripple"
                                           href="/aqi-us">
                                            <Avatar image="images/flags/us.svg" shape="circle"/>
                                            <span class="font-medium ml-2">USA Index</span>
                                        </a>
                                        <a class="flex items-center cursor-pointer pl-4 p-3 rounded text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors p-ripple"
                                           href="/aqi-uk">
                                            <Avatar image="images/flags/uk.svg" shape="circle"/>
                                            <span class="font-medium ml-2">UK Index</span>
                                        </a>
                                        <a class="flex items-center cursor-pointer pl-4 p-3 rounded text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors p-ripple"
                                           href="/aqi-eu">
                                            <Avatar image="images/flags/eu.svg" shape="circle"/>
                                            <span class="font-medium ml-2">EU Index</span>
                                        </a>
                                    </AccordionContent>
                                </AccordionPanel>
                            </Accordion>
                        </li>
                    </ul>
                </div>
            </div>
        </template>
    </Drawer>

    <Button v-show="btnVisible" icon="pi pi-bars" @click="displayDrawer" style="position: absolute; z-index: 9999999; left: 50px; top: 50px"/>
</template>

<style scoped>
.p-accordionpanel {
    box-shadow: initial;
}
</style>

<script>
import Drawer from "primevue/drawer";
import Button from "primevue/button";
import Tree from "primevue/tree";
import Accordion from "primevue/accordion";
import AccordionContent from "primevue/accordioncontent";
import AccordionHeader from "primevue/accordionheader";
import AccordionPanel from "primevue/accordionpanel";
import Avatar from "primevue/avatar";

export default {
    name: "MapMenu",
    components: {
        Avatar,
        Drawer,
        Button,
        Accordion,
        AccordionPanel,
        AccordionHeader,
        AccordionContent
    },
    emits: ['closed', 'opened'],
    data() {
        return {
            visible: false,
            btnVisible: true,
        }
    },
    methods: {
        drawerClosed() {
            setTimeout(() => {
                this.btnVisible = true;
            }, 170);
            this.$emit('closed');
        },
        displayDrawer() {
            this.visible = true;
            this.btnVisible = false;
            this.$emit('opened');
        }
    }
}
</script>
