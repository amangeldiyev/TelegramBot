<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Attachments\Audio;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Support\Facades\Storage;

class TextToSpeech extends Conversation
{
    /**
     * First question
     */
    public function askText()
    {
        $this->ask('Send text to transform', function (Answer $answer) {
            $text = $answer->getText();

            $tts = new \App\Api\VoiceRSS;
            $voice = $tts->speech([
                'key' => '865b0c26f4374962b7b6a3851414c516',
                'hl' => 'en-us',
                'src' => $text,
                'r' => '0',
                'c' => 'mp3',
                'f' => '44khz_16bit_stereo',
                'ssml' => 'false',
                'b64' => 'true'
            ]);

            $filename = time() . '.mp3';

            Storage::disk('public')->put('tts/'. $filename, base64_decode($voice['response']));

            $attachment = new Audio(config('app.url') . '/storage/tts/' . $filename, [
                'custom_payload' => true,
            ]);

            $message = OutgoingMessage::create('Here is your audio file')
                ->withAttachment($attachment);

            $this->say($message);
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askText();
    }
}
