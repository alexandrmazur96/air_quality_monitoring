<?php

declare(strict_types=1);

namespace Mazur\Console\Commands\Telegram;

use Illuminate\Console\Command;
use Telegram\Bot\Api;

final class DeleteWebhook extends Command
{
    protected $signature = 'telegram:delete-webhook';

    protected $description = 'Delete telegram webhook';

    public function handle(): void
    {
        $api = new Api(config('telegram.bots.air-quality-ua.token'));
        $result = $api->deleteWebhook();

        $result ? $this->info('Webhook deleted successfully') : $this->error('Failed to delete webhook');
    }
}
