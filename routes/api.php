<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Mazur\Http\Controllers\TelegramController;

Route::post('/telegram/webhook', [TelegramController::class, 'handleWebhook']);
