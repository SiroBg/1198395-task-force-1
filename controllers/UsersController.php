<?php

namespace app\controllers;

use app\models\Users;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{
    public function actionView(int $id)
    {
        $user = Users::findOne($id);

        if ($user === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        return $this->render('view', ['user' => $user]);
    }
}
