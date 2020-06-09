<?php
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('/cr', BotManController::class.'@startConversation');
