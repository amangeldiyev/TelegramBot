<?php

namespace App\Conversations;

use App\Notification as AppNotification;
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
        $question = Question::create("Notifications")
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Set up notification')->value('set-up'),
                Button::create('Change message text')->value('text'),
                Button::create('Notify')->value('notify'),
                Button::create('Subscribe')->value('subscribe'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            $user = $this->bot->getUser();

            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'set-up') {
                    $code = Str::random(5);

                    while (AppNotification::where('code', $code)->exists()) {
                        $code = Str::random(5);
                    }

                    AppNotification::create([
                        'userID' => $user->getId(),
                        'code' => $code,
                        'text' => 'New notifiction'
                    ]);

                    $this->say('Your code: ' . $code);
                } elseif ($answer->getValue() === 'text') {
                    $notifications = AppNotification::where('userID', $user->getId())
                                        ->get()
                                        ->pluck('desc', 'code')
                                        ->all();
                    
                    $buttons = [];

                    foreach ($notifications as $code => $desc) {
                        $buttons[] = Button::create($desc)->value($code);
                    }

                    $select_notification = Question::create("Select notification")
                        ->addButtons($buttons);
                    
                    $this->ask($select_notification, function (Answer $selected) {
                        if ($selected->isInteractiveMessageReply()) {
                            $selected_code = $selected->getValue();

                            $this->ask('Send text to transform', function (Answer $answer) use ($selected_code) {
                                $text = $answer->getText();

                                AppNotification::where('code', $selected_code)->update([
                                    'text' => $text
                                ]);
                    
                                $this->say('Text changed to: ');
                                $this->say($text);
                            });
                        }
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
