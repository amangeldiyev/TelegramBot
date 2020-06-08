<?php
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $user = $bot->getUser();
    $bot->reply(`Hello $user->getFirstName()!`);
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');
