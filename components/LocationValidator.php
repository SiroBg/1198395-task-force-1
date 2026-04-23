<?php

namespace app\components;

use app\models\User;
use GuzzleHttp\Client;
use Yii;
use yii\helpers\Json;
use yii\validators\Validator;

class LocationValidator extends Validator
{
    public string $apiKey;
    private string $apiUrl = 'https://geocode-maps.yandex.ru/v1/';

    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;
        $client = new Client(['verify' => false]);
        try {
            $response = $client->request('GET', $this->apiUrl, [
                'query' => [
                    'apikey' => $this->apiKey,
                    'geocode' => $value,
                    'format' => 'json',
                    'results' => 1,
                ],
            ]);
        } catch (\Throwable $exception) {
            exit($exception->getMessage());
        }
        $data = Json::decode($response->getBody());

        $pos = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];

        $cityComponents = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['Components'];

        $userCity = User::find()->where(['id' => Yii::$app->user->id])->one();

        $isRightCity = array_any($cityComponents, function ($value) use ($userCity) {
            return in_array($userCity->city->name, $value);
        });

        if (!$isRightCity) {
            $this->addError($model, $attribute, 'Выберете адрес, совпадающий с вашим городом');
        }

        [$model->long, $model->lat] = explode(' ', $pos);
        $model->city_id = $userCity->city_id;
    }
}
