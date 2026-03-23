<?php

namespace common\fixtures;

use yii\test\ActiveFixture;

class TaskFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Tasks';

    public $depends = ['app\fixtures\UserFixture'];
}
