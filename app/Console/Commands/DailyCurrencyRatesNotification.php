<?php

namespace App\Console\Commands;

use App\Api\CurrencyRates;
use App\User;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Console\Command;

class DailyCurrencyRatesNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily currency rates notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $botman = app('botman');

        $users = User::all();

        $rates = CurrencyRates::getRates();

        foreach ($users as $user) {
            foreach ($rates as $rate) {
                $message = $rate['title'] . ": " . $rate['description'] . "(" . $rate['change'] . ")";
                $botman->say($message, $user->userID, TelegramDriver::class);
            }
        }

    }
}
