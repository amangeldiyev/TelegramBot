<?php

namespace App\Conversations;

use App\Api\CurrencyRates as ApiCurrencyRates;
use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class CurrencyRates extends Conversation
{
    /**
     * First question
     */
    public function askReason()
    {
        $question = Question::create("Currency rates for USD, EUR, RUB")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Currency Rates')->value('rates'),
                Button::create('Subscribe (Daily notification at 11:00)')->value('subscribe'),
                Button::create('Unsubscribe')->value('unsubscribe'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'rates') {
                    $rates = ApiCurrencyRates::getRates();

                    foreach ($rates as $rate) {
                        $this->say($rate['title'] . ": " . $rate['description'] . "(" . $rate['change'] . ")");
                    }
                } elseif ($answer->getValue() === 'subscribe') {
                    $user = $this->bot->getUser();

                    User::updateOrCreate(
                        ['userID' => $user->getId()],
                        ['name' => $user->getFirstName(), 'username' => $user->getUsername()]
                    );

                    $this->say('Successfully subscribed!');
                } else {
                    $user = $this->bot->getUser();

                    User::where('userID', $user->getId())->delete();

                    $this->say('Successfully unsubscribed!');
                }
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askReason();
    }
}
