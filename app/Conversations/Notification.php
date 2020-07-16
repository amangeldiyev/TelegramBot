<?php

namespace App\Conversations;

use App\Notification as AppNotification;
use App\NotificationSubscription;
use Illuminate\Support\Str;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\Drivers\Telegram\TelegramDriver;

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
                        'desc' => 'Notification with code: ' . $code,
                        'text' => 'New notifiction message'
                    ]);

                    $this->say('Your code: ' . $code);
                } elseif ($answer->getValue() === 'text') {
                    $this->ask($this->getAvailableNotifications($user->getId()), function (Answer $answer) {
                        if ($answer->isInteractiveMessageReply()) {
                            $selected_code = $answer->getValue();

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
                    $this->ask($this->getAvailableNotifications($user->getId()), function (Answer $answer) {
                        if ($answer->isInteractiveMessageReply()) {
                            $selected_code = $answer->getValue();

                            $users = NotificationSubscription::where('code', $selected_code)->get();
                            $text = AppNotification::where('code', $selected_code)->first()->text;

                            foreach ($users as $receiver) {
                                sleep(1);

                                $$this->say($text, $receiver->userID, TelegramDriver::class);
                            }
                        }
                    });
                } elseif ($answer->getValue() === 'subscribe') {
                    $this->ask('Enter notification code', function (Answer $answer) use ($user) {
                        $code = $answer->getText();

                        if (AppNotification::where('code', $code)->exists()) {
                            NotificationSubscription::create([
                                'userID' => $user->getId(),
                                'code' => $code
                            ]);

                            $this->say('Subscribed to: ' . $code);
                        } else {
                            $this->say('Code not found!');
                        }
                    });
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

    private function getAvailableNotifications($userID)
    {
        $notifications = AppNotification::where('userID', $userID)
            ->get()
            ->pluck('desc', 'code')
            ->all();

        $buttons = [];

        foreach ($notifications as $code => $desc) {
            $buttons[] = Button::create($desc)->value($code);
        }

        $question = Question::create("Select notification")
            ->addButtons($buttons);

        return $question;
    }
}
