<?php

namespace App\Conversations;

use App\User;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use GuzzleHttp\Client;

class CurrencyRates extends Conversation
{
    /**
     * First question
     */
    public function askReason()
    {
        $question = Question::create("Huh - you woke me up. What do you need?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Currency Rates')->value('rates'),
                Button::create('Subscribe')->value('subscribe'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'rates') {
                    $client = new Client();

                    $response = $client->request('GET', 'https://nationalbank.kz/rss/rates_all.xml?switch=russian');

                    $xml = new \SimpleXMLElement($response->getBody()->getContents());

                    $usd = "USD: " . (string)$xml->channel->item[4]->description . "(" . $xml->channel->item[4]->change . ")";
                    $eur = "EUR: " . (string)$xml->channel->item[5]->description . "(" . $xml->channel->item[5]->change . ")";
                    $rub = "RUB: " . (string)$xml->channel->item[14]->description . "(" . $xml->channel->item[14]->change . ")";

                    $this->say($usd);
                    $this->say($eur);
                    $this->say($rub);
                } else {
                    $user = $this->bot->getUser();

                    User::updateOrCreate(
                        ['userID' => $user->getId(),],
                        ['name' => $user->getFirstName(), 'username' => $user->getUsername()]
                    );

                    $this->say('Successfully subscribed!');
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
