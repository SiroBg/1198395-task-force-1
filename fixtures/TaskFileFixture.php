<?php

namespace common\fixtures;

use yii\test\ActiveFixture;

class TaskFileFixture extends ActiveFixture
{
    public $modelClass = 'app\models\TaskFiles';

    public $depends = ['app\fixtures\FileFixture', 'app\fixtures\TaskFixture'];
}
