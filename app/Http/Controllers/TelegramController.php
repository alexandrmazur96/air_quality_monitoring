<?php

declare(strict_types=1);

namespace Mazur\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Mazur\Application\AirQuality\ApiIntegrations\Enum\Provider;
use Mazur\Application\AirQuality\AqiCalculator\AqiCalculator;
use Mazur\Application\AirQuality\AqiCalculator\Enums\AqiType;
use Mazur\Application\AirQuality\SourceUnion\MaxSelectionSourceUnion;
use Mazur\Application\Repository\AirQuality\AirQualityRepository;
use Mazur\Application\Repository\User\UsersLocationRepository;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;
use Throwable;

final class TelegramController extends Controller
{
    public function __construct(
        private UsersLocationRepository $usersLocationRepository,
        private AirQualityRepository $airQualityRepository,
        private MaxSelectionSourceUnion $maxSelectionSourceUnion,
        private AqiCalculator $aqiCalculator
    ) {
    }

    public function handleWebhook(
        Request $request,
        ResponseFactory $responseFactory
    ): Response {
        Log::debug('Telegram webhook request', $request->all());

        $tg = new Api(config('telegram.bots.air-quality-ua.token'));
        $update = $tg->getWebhookUpdate(false);
        if ($update->isType('message')) {
            /** @var Message $message */
            $message = $update->getMessage();

            if ($message->getText() === '/start') {
                $tg->sendMessage([
                    'chat_id' => $message->getChat()->getId(),
                    'text' => 'Hey there! I am Air Quality Monitoring Bot. ' .
                              'I can help you to monitor air quality in your area. ' .
                              'Please, send me your location to get started.',
                ]);
            }

            if ($message->getLocation()) {
                try {
                    $userLocation = $this->usersLocationRepository->store(
                        (string)$message->getChat()->getId(),
                        $message->getLocation()->getLatitude(),
                        $message->getLocation()->getLongitude()
                    );
                } catch (Throwable $e) {
                    Log::error('Failed to store user location', [
                        'chat_id' => $message->getChat()->getId(),
                        'error' => $e->getMessage(),
                    ]);

                    $tg->sendMessage([
                        'chat_id' => $message->getChat()->getId(),
                        'text' => 'Sorry, I failed to store your location. Please, try again later.',
                    ]);

                    return $responseFactory->noContent(200);
                }

                $tg->sendMessage([
                    'chat_id' => $message->getChat()->getId(),
                    'text' => sprintf(
                        'I got your location. The nearest city to you is %s. Let me check the air quality for you...',
                        $userLocation->nearestCity->name
                    ),
                ]);

                $latestRecordsForCity = $this->getAirQualityResultsForCity($userLocation->nearestCity->id);
                if ($latestRecordsForCity->isEmpty()) {
                    $tg->sendMessage([
                        'chat_id' => $message->getChat()->getId(),
                        'text' => 'Sorry, there are no air quality information for your area yet. ' .
                                  'We\'ll notify you once available.',
                    ]);
                } else {
                    $indexStrRepresentation = $this->aqiCalculator->getStringRepresentation(
                        $latestRecordsForCity->first()->aqiUs,
                        AqiType::US
                    );
                    $tg->sendMessage([
                        'chat_id' => $message->getChat()->getId(),
                        'text' => sprintf(
                            'According to US Air Quality the air quality in your area is %s. %s',
                            strtolower($indexStrRepresentation->index),
                            $indexStrRepresentation->description
                        ),
                    ]);

                    $tg->sendMessage([
                        'chat_id' => $message->getChat()->getId(),
                        'text' => 'Details: https://air-quality.com.ua/city/' . $userLocation->nearestCity->id,
                    ]);
                }
            }
        }
        return $responseFactory->noContent(200);
    }

    private function getAirQualityResultsForCity(int $cityId): Collection
    {
        $openWeatherIndexes = $this->airQualityRepository->getLatestAirQualityIndexForCity(
            $cityId,
            Provider::OPEN_WEATHER
        );
        $weatherApiIndexes = $this->airQualityRepository->getLatestAirQualityIndexForCity(
            $cityId,
            Provider::WEATHER_API
        );
        $unitedIndexes = $this->maxSelectionSourceUnion->uniteRaws($openWeatherIndexes, $weatherApiIndexes);

        foreach ($unitedIndexes as $key => $index) {
            if ($index->aqiUs === null) {
                $aqiUk = $this->aqiCalculator->calculate($index->toAirQuality(), AqiType::UK);
                $aqiUs = $this->aqiCalculator->calculate($index->toAirQuality(), AqiType::US);
                $aqiEu = $this->aqiCalculator->calculate($index->toAirQuality(), AqiType::EUROPE);
                $index = $index->withAqiIndexes($aqiUk, $aqiUs, $aqiEu);
                $unitedIndexes[$key] = $index;
            }
        }

        return $unitedIndexes;
    }
}
