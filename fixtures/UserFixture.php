<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\models\User';
    public $dataFile = __DIR__ . '/data/user.php';

    public $depends = ['app\fixtures\FileFixture'];
}
