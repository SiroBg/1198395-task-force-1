<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class TaskFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Task';
    public $dataFile = __DIR__ . '/data/task.php';

    public $depends = ['app\fixtures\UserFixture'];
}
