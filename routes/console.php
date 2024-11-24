<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('air-quality:pull:open-weather')->hourly();
Schedule::command('air-quality:pull:weather-api')->hourly();
Schedule::command('notify:telegram:air-quality-changes:all')->hourlyAt(15);
