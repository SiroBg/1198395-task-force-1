<?php

namespace app\components;

use GuzzleHttp\Client;
use Yii;
use yii\base\Component;
use yii\helpers\Json;


class YandexGeocoder extends Component
{
    private string $apiUrl = 'https://geocode-maps.yandex.ru/v1/';

    public function getObjectData(string $address): array
    {
        $result = [];
        $client = new Client(['verify' => false]);
        try {
            $response = $client->request('GET', $this->apiUrl, [
                'query' => [
                    'apikey'  => Yii::$app->params['yandexJsApiKey'],
                    'geocode' => $address,
                    'format'  => 'json',
                    'results' => 1,
                ],
            ]);
        } catch (\Throwable $exception) {
            exit($exception->getMessage());
        }
        $data = Json::decode($response->getBody());

        $geoObject = $data['response']['GeoObjectCollection']
                     ['featureMember'][0]['GeoObject'] ?? null;

        if ($geoObject) {
            $result['coordinates'] = explode(
                ' ',
                $geoObject['Point']['pos']
            );
            $result['addressComponents']
                = $geoObject['metaDataProperty']['GeocoderMetaData']
                  ['Address']['Components'];
        }
        return $result;
    }
}