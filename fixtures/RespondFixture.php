<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class RespondFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Responds';
    public $dataFile = __DIR__ . '/data/respond.php';

    public $depends = ['app\fixtures\TaskFixture', 'app\fixtures\UserFixture'];
}
