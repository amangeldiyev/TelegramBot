<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use GuzzleHttp\Client;

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');

Route::get('set-webhook', function () {
    $client = new Client();

    $response = $client->request('POST', 'https://api.telegram.org/bot1110008093:AAExIGim5IkLHV-NCR_HHn7ZYBsFOYSO8RM/setWebhook', [
        'form_params' => [
            'url' => 'https://rukzinternational.kz/botman'
        ]
    ]);

    dd($response->getBody()->getContents());
});
