<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class FileFixture extends ActiveFixture
{
    public $modelClass = 'app\models\File';
    public $dataFile = __DIR__ . '/data/file.php';

}
