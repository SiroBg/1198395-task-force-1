<?php

/**
 * @link      https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   https://www.yiiframework.com/license/
 */

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since  2.0
 */
class AddTaskAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl  = '@web';

    public $js = [];

    public function init(): void
    {
        parent::init();

        $this->js[] = Yii::$app->params['yandexSuggestCDN'];
        $this->js[] = 'js/yandexSuggest.js';
    }
}
