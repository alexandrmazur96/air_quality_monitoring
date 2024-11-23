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
                            <a class="flex items-center cursor-pointer pl-4 p-3 rounded text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors"
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
                    </ul>
                </div>

                <div class="mt-auto">
                    <hr class="mb-4 mx-4 border-t border-0 border-surface-200 dark:border-surface-700" />
                    <a class="m-4 flex items-center cursor-pointer p-4 gap-2 rounded text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors">
                        <a target="_blank" href="https://www.flaticon.com/free-icons/clean-air" title="clean air icons">Clean air icons created by Culmbio - Flaticon</a>
                    </a>
                </div>
            </div>
        </template>
    </Drawer>

    <Button v-show="btnVisible" icon="pi pi-bars" @click="displayDrawer" style="position: absolute; z-index: 9999999; left: 50px; top: 50px"/>
</template>

<script>
import Drawer from "primevue/drawer";
import Button from "primevue/button";

export default {
    name: "MapMenu",
    components: {
        Drawer,
        Button
    },
    emits: ['closed', 'opened'],
    data() {
        return {
            visible: false,
            btnVisible: true
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
