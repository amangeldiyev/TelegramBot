<?php

namespace App\Conversations;

use Illuminate\Support\Str;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class Notification extends Conversation
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
                Button::create('Set up notification')->value('set-up'),
                Button::create('Change message text')->value('text'),
                Button::create('Notify')->value('notify'),
                Button::create('Subscribe')->value('subscribe'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'set-up') {
                    $code = Str::random(5);

                    // check for availability

                    // save to DB

                    $this->say('Your code: ' . $code);
                } elseif ($answer->getValue() === 'text') {
                    $this->ask('Send text to transform', function (Answer $answer) {
                        $text = $answer->getText();

                        // Save text
            
                        $this->say('Text changed to: ');
                        $this->say($text);
                    });
                } elseif ($answer->getValue() === 'notify') {
                    // notify users
                } elseif ($answer->getValue() === 'subscribe') {
                    // subscribe
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
