<?php

namespace app\components;

use GuzzleHttp\Client;
use Yii;
use yii\base\Component;
use yii\helpers\Json;


class YandexGeocoder extends Component
{
    private string $apiUrl = 'https://geocode-maps.yandex.ru/v1/';

    /**
     * Возвращает координаты и полный адрес переданной строки.
     * @param  string  $address  Адрес.
     * @return array
     * @throws \Exception
     */
    public function getObjectData(string $address): array
    {
        $result = [];
        $client = new Client(['verify' => false]);
        try {
            $response = $client->request('GET', $this->apiUrl, [
                'query' => [
                    'apikey'  => Yii::$app->params['yandexGeocoderApiKey'] ?? '',
                    'geocode' => $address,
                    'format'  => 'json',
                    'results' => 1,
                ],
            ]);
        } catch (\Throwable $exception) {
            exit($exception->getMessage());
        }
        $data = Json::decode($response->getBody());

        if ( ! isset(
            $data['response'], $data['response']['GeoObjectCollection'], $data['response']['GeoObjectCollection']
            ['featureMember'], $data['response']['GeoObjectCollection']
                               ['featureMember'][0], $data['response']['GeoObjectCollection']
                                                     ['featureMember'][0]['GeoObject']
        )) {
            throw new \Exception('Ошибка ответа геокодера');
        }
        $geoObject = $data['response']['GeoObjectCollection']
                     ['featureMember'][0]['GeoObject'];

        if ($geoObject) {
            $result['coordinates'] = explode(
                ' ',
                isset($geoObject['Point'], $geoObject['Point']['pos']) ?
                    $geoObject['Point']['pos'] : []
            );
            $result['fullAddress'] = isset(
                $geoObject['metaDataProperty'],
                $geoObject['metaDataProperty']['GeocoderMetaData'],
                $geoObject['metaDataProperty']['GeocoderMetaData']
                ['text']
            ) ? $geoObject['metaDataProperty']['GeocoderMetaData']
            ['text'] : '';
        }
        return $result;
    }
}