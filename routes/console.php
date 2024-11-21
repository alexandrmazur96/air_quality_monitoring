<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('air-quality:pull:open-weather')->hourly();
Schedule::command('air-quality:pull:weather-api')->hourly();
