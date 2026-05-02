<?php

namespace app\controllers;

use app\models\User;
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
        $user = User::findOne($id);

        if ($user === null || ! $user->is_executor) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        $userReviews = $user->reviewsAsExecutor;

        return $this->render('view', ['user' => $user, 'userReviews' => $userReviews]);
    }

    public function actionLogout(): \yii\web\Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
