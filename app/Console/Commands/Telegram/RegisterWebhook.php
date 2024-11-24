<?php

declare(strict_types=1);

namespace Mazur\Console\Commands\Telegram;

use Illuminate\Console\Command;
use Telegram\Bot\Api;

final class RegisterWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';

    protected $description = 'Set telegram webhook';

    public function handle(): void
    {
        $api = new Api();
        $result = $api->setWebhook([
            'url' => config('telegram.bots.air-quality-ua.webhook_url'),
            'secret_token' => config('telegram.bots.air-quality-ua.webhook_auth_token'),
        ]);

        $result ? $this->info('Webhook set successfully') : $this->error('Failed to set webhook');
    }
}
