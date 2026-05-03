<?php

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class AddTaskAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl  = '@web';

    public $js = [];

    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        parent::init();

        $this->js[] = Yii::$app->params['yandexSuggestCDN'];
        $this->js[] = 'js/yandexSuggest.js';
    }
}
