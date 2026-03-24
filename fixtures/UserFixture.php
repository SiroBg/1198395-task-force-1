<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Users';
    public $dataFile = __DIR__ . '/data/user.php';

    public $depends = ['app\fixtures\FileFixture'];
}
