<?php

namespace common\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Users';

    public $depends = ['app\fixtures\FileFixture'];
}
