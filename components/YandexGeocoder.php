<?php

namespace app\components;

use app\models\User;
use Yii;
use yii\base\Component;
use GuzzleHttp\Client;
use yii\helpers\Json;
use GuzzleHttp\TransferStats;

class YandexGeocoder extends Component
{
    public string $apiKey;
    private string $apiUrl = 'https://geocode-maps.yandex.ru/v1/';

    public function getCoordinates($address)
    {
        $client = new Client(['verify' => false]);
        try {
            $response = $client->request('GET', $this->apiUrl, [
                'query' => [
                    'apikey'  => $this->apiKey,
                    'geocode' => $address,
                    'format'  => 'json',
                    'results' => 1,
                ]
            ]);
        } catch (\Throwable $exception) {
            exit($exception->getMessage());
        }
        $data = Json::decode($response->getBody());

        $pos
            = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']
            ?? null;

        if ($pos) {
            return explode(' ', $pos);
        }
        return null;
    }
}