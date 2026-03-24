<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class UserCategoryFixture extends ActiveFixture
{
    public $modelClass = 'app\models\UserCategories';
    public $dataFile = __DIR__ . '/data/userCategory.php';

    public $depends = ['app\fixtures\UserFixture'];
}
