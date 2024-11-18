<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('air-quality:pull:open-weather')->twiceDaily(8, 20);
