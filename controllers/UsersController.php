<?php

namespace app\controllers;

use app\models\Users;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $user = Users::findOne($id);

        if ($user === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        return $this->render('view', ['user' => $user]);
    }

    public function actionLogout(): \yii\web\Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
