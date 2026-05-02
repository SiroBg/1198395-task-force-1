<?php

namespace app\controllers;

use app\models\Task;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\Controller;

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

    public function actionIndex(): string
    {
        $user = User::findOne(Yii::$app->user->id);
        $provider = new ActiveDataProvider([
            'query' => Task::find(),
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);
        $status = Yii::$app->request->get('status');
        if (!in_array($status, $this->allowedStatuses)) {
            $status = $user->is_executor ? 'active' : 'new';
        }

        if (!$user->is_executor) {
            $provider->query->where(['author_id' => $user->id]);
        } else {
            $provider->query->where(['executor_id' => $user->id]);
        }
        $title = '';
        switch ($status) {
            case 'active':
                $provider->query->andWhere(['status' => Task::STATUS_ACTIVE]);
                $title = 'В процессе';
                break;
            case 'closed':
                $provider->query->andWhere([
                    'status' => [
                        Task::STATUS_FINISHED,
                        Task::STATUS_FAILED,
                        Task::STATUS_CANCELED,
                    ],
                ]);
                $title = 'Закрытые';
                break;
            case 'expired':
                $provider->query->andWhere(['status' => Task::STATUS_ACTIVE])
                ->andWhere(['<', 'expire_date', new Expression('CURDATE()'),]);
                $title = 'Просрочено';
                break;
            case 'new':
                $provider->query->andWhere(['status' => Task::STATUS_NEW]);
                $title = 'Новые';
                break;
        }

        return $this->render('index', [
            'provider' => $provider,
            'user' => $user,
            'title' => $title,
            'status' => $status,
        ]);
    }
}
