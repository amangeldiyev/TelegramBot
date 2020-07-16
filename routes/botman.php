<?php
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('/cr', BotManController::class.'@currencyRates');

$botman->hears('/tts', BotManController::class.'@textToSpeech');

$botman->hears('/notif', BotManController::class.'@notification');
