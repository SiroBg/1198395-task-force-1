<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class TaskFileFixture extends ActiveFixture
{
    public $modelClass = 'app\models\TaskFile';
    public $dataFile = __DIR__ . '/data/taskFile.php';

    public $depends = ['app\fixtures\FileFixture', 'app\fixtures\TaskFixture'];
}
