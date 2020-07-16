<?php

namespace App\Api;

use GuzzleHttp\Client;

class CurrencyRates
{
    const URL = 'https://nationalbank.kz/rss/rates_all.xml?switch=russian';

    protected static function rates()
    {
        $client = new Client();

        $response = $client->request('GET', self::URL);

        $xml = new \SimpleXMLElement($response->getBody()->getContents());

        return $xml;
    }

    public static function getRates($currency_ids = [0,1,2])
    {
        $xml = self::rates();

        $rates = [];

        foreach ($currency_ids as $currency_id) {
            $rates[] = [
                'title' => (string)$xml->channel->item[$currency_id]->title,
                'description' => (string)$xml->channel->item[$currency_id]->description,
                'change' => (string)$xml->channel->item[$currency_id]->change
            ];
        }

        return $rates;
    }
}
