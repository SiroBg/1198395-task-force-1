<?php

namespace app\controllers;

use app\models\Task;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\Response;

class MyTasksController extends Controller
{
    private array $allowedStatuses
        = [
            'new',
            'active',
            'expired',
            'closed',
        ];

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

    public function actionIndex(?string $status = null): string
    {
        $user = User::findOne(Yii::$app->user->id);
        $provider = new ActiveDataProvider([
            'query'      => Task::find(),
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort'       => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        if (!$user->is_executor) {
            $provider->query->where(['author_id' => $user->id]);
        } else {
            $provider->query->where(['executor_id' => $user->id]);
        }

        if (!in_array($status, $this->allowedStatuses)) {
            $status = $user->is_executor ? 'active' : 'new';
        }

        switch ($status) {
            case 'active':
                $provider->query->andWhere(['status' => Task::STATUS_ACTIVE]);
                break;
            case 'closed':
                $provider->query->andWhere([
                    'status' => [
                        Task::STATUS_FINISHED,
                        Task::STATUS_FAILED,
                        Task::STATUS_CANCELED
                    ]
                ]);
                break;
            case 'expired':
                $provider->query->andWhere([
                    'status' => Task::STATUS_ACTIVE,
                    'expire_date' < 'CURDATE()'
                ]);
                break;
            case 'new':
                $provider->query->andWhere(['status' => Task::STATUS_NEW]);
                break;
        }

        return $this->render('index', [
            'provider' => $provider,
            'user'     => $user,
        ]);
    }
}
