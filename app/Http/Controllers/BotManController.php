<?php

namespace App\Http\Controllers;

use App\Conversations\CurrencyRates;
use App\Conversations\Notification;
use App\Conversations\TextToSpeech;
use BotMan\BotMan\BotMan;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Http\Request;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function currencyRates(BotMan $bot)
    {
        $bot->startConversation(new CurrencyRates());
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function textToSpeech(BotMan $bot)
    {
        $bot->startConversation(new TextToSpeech());
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function notification(BotMan $bot)
    {
        $bot->startConversation(new Notification());
    }

    /**
     * Notify
     * @param Botman $bot
     * @param $key
     */
    public function notify($key)
    {
        if ($key != config('botman.telegram.key')) {
            return;
        }

        $botman = app('botman');

        $botman->say(request('message'), config('botman.telegram.user_id'), TelegramDriver::class);
    }
}
