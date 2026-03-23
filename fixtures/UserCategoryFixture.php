<?php

namespace common\fixtures;

use yii\test\ActiveFixture;

class UserCategoryFixture extends ActiveFixture
{
    public $modelClass = 'app\models\UserCategories';

    public $depends = ['app\fixtures\UserFixture'];
}
