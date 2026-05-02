<?php

namespace app\controllers;

use app\models\Task;
use app\models\TaskSearch;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\Controller;

class MyTasksController extends Controller
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

    public function actionIndex(): string
    {
        $user       = User::findOne(Yii::$app->user->id);
        $status     = Yii::$app->request->get('status');
        $taskSearch = new TaskSearch();
        $provider   = $taskSearch->getUsersTasksProvider($user, $status);


        return $this->render('index', [
            'provider' => $provider,
            'user'     => $user,
            'status'   => $status,
        ]);
    }
}
