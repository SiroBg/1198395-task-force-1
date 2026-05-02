<?php

require_once __DIR__.'/../init.php';

return [
    'yandexGeocoderApiKey' => $_ENV['YANDEX_GEOCODER_API_KEY'],
    'yandexSuggestApiKey'  => $_ENV['YANDEX_SUGGEST_API_KEY'],
    'yandexMapJsCDN'       => 'https://api-maps.yandex.ru/2.1/?apikey='
                              .$_ENV['YANDEX_GEOCODER_API_KEY'].'&lang=ru_RU',
    'yandexSuggestCDN'     => 'https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey='
                              .$_ENV['YANDEX_GEOCODER_API_KEY'].'&suggest_apikey='
                              .$_ENV['YANDEX_SUGGEST_API_KEY'],
];
