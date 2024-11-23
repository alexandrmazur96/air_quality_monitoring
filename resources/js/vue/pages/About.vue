<template>
    <AppMenu/>

    <div class="grid grid-nogutter p-3">
        <div class="md:col-8 md:col-offset-2 col-12">
                <h1 class="text-3xl md:mt-5 mt-2 mb-3">
                    The project focuses on collecting and rendering on a map air quality data
                </h1>
                <p class="mb-3">
                    The data is then aggregated using a Max Selection algorithm to choose the most relevant values
                    from different sources for more accurate monitoring.
                </p>
                <p class="mb-3">The system provides access to multiple air quality indices:</p>

                <ul class="list-disc mb-3">
                    <li class="ml-5">US AQI (Air Quality Index)</li>
                    <li class="ml-5">UK AQI</li>
                    <li class="ml-5">EU AQI</li>
                </ul>

                <p class="mb-3">
                    Additionally, to monitor air quality based on your current location, users can utilize our
                    <a href="/chat-bot" class="font-medium text-blue-600 underline dark:text-blue-500 hover:no-underline">Telegram Chatbot</a>.
                    The bot allows you to receive real-time air quality updates and alerts, providing a convenient way
                    to stay
                    informed about the air quality wherever you are.
                </p>

                <p class="text-sm mt-5 mb-3">
                    This is the master's thesis project by <a href="https://github.com/alexandrmazur96" class="font-medium text-blue-600 underline dark:text-blue-500 hover:no-underline" target="_blank">Oleksandr Mazur</a>, a student at the University of
                    Odesa I. I. Mechnykov National University, Ukraine (previously, Odesa State Environmental
                    University).
                </p>

                <p class="mb-5">
                    The list of supported cities described on the <a href="/supported-cities" class="font-medium text-blue-500 underline dark:text-blue-500 hover:no-underline">Supported cities</a> page.
                </p>

                <p class="text-lg">Powered By:</p>
                <ul class="list-disc">
                    <li class="ml-5">
                        <a class="font-medium text-blue-600 underline dark:text-blue-500 hover:no-underline" target="_blank" href="https://www.flaticon.com/free-icons/clean-air" title="clean air icons">
                            Clean air icons created by Culmbio - Flaticon
                        </a>
                    </li>
                    <li class="ml-5">
                        <a class="font-medium text-blue-600 underline dark:text-blue-500 hover:no-underline" target="_blank" href="https://www.weatherapi.com/" title="Free Weather API">Free Weather API</a>
                    </li>
                    <li class="ml-5">
                        <a class="font-medium text-blue-600 underline dark:text-blue-500 hover:no-underline" target="_blank" href="https://openweathermap.org/" title="Open Weather">Open Weather</a>
                    </li>
                    <li class="ml-5">
                        <a class="font-medium text-blue-600 underline dark:text-blue-500 hover:no-underline" target="_blank" href="https://primevue.org/" title="Prime vue"><i class="pi pi-prime mr-1"></i>Prime Vue</a>
                    </li>
                </ul>
        </div>
    </div>
</template>

<script>
import AppMenu from "../components/AppMenu.vue";

export default {
    name: 'About',
    components: {AppMenu},
    data() {
        return {
            title: 'About'
        }
    }
}
</script>
