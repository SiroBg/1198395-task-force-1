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
class ViewTaskAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl  = '@web';

    public $js = [];

    public function init(): void
    {
        parent::init();
        
        $this->js[] = Yii::$app->params['yandexMapJsCDN'];
        $this->js[] = 'js/yandexMap.js';
    }
}
