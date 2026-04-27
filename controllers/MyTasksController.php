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
    private User $user;
    private ActiveDataProvider $provider;
    private array $actions = [];
    private string $actionTitle = '';

    public function __construct($id, $module)
    {
        $this->user = User::findOne(Yii::$app->user->id);
        $this->provider = new ActiveDataProvider([
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

        if ($this->user->is_executor) {
            $this->provider->query->where(['executor_id' => $this->user->id]);
            $this->actions = [
                'active'  => [
                    'name'   => 'В процессе',
                    'action' => 'my-tasks/active'
                ],
                'expired' => [
                    'name'   => 'Просрочено',
                    'action' => 'my-tasks/expired'
                ],
                'closed'  => [
                    'name'   => 'Закрытые',
                    'action' => 'my-tasks/closed'
                ],
            ];
        } else {
            $this->provider->query->where(['author_id' => $this->user->id]);
            $this->actions = [
                'new'    => ['name' => 'Новые', 'action' => 'my-tasks/new'],
                'active' => [
                    'name'   => 'В процессе',
                    'action' => 'my-tasks/active'
                ],
                'closed' => [
                    'name'   => 'Закрытые',
                    'action' => 'my-tasks/closed'
                ],
            ];
        }

        parent::__construct($id, $module);
    }

    public function actionIndex(): Response
    {
        if ($this->user->is_executor) {
            return Yii::$app->response->redirect(['/my-tasks/active']);
        }
        if (!$this->user->is_executor) {
            return Yii::$app->response->redirect(['/my-tasks/new']);
        }
        return $this->goHome();
    }

    public function actionNew(): string
    {
        $this->provider->query->andWhere(['status' => Task::STATUS_NEW]);
        $this->actionTitle = 'Новые';

        return $this->render('index', [
            'provider' => $this->provider,
            'actions'  => $this->actions,
            'title'    => $this->actionTitle,
        ]);
    }

    public function actionActive(): string
    {
        $this->provider->query->andWhere(['status' => Task::STATUS_ACTIVE]);
        $this->actionTitle = 'В процессе';

        return $this->render('index', [
            'provider' => $this->provider,
            'actions'  => $this->actions,
            'title'    => $this->actionTitle,
        ]);
    }

    public function actionClosed(): string
    {
        $this->provider->query->andWhere(
            ['status' => [Task::STATUS_FINISHED, Task::STATUS_FAILED]]
        );
        $this->actionTitle = 'Закрытые';

        if (!$this->user->is_executor) {
            $this->provider->query->orWhere(['status' => Task::STATUS_CANCELED]
            );
        }

        return $this->render('index', [
            'provider' => $this->provider,
            'actions'  => $this->actions,
            'title'    => $this->actionTitle,
        ]);
    }

    public function actionExpired(): string
    {
        $this->provider->query->where(
            ['status' => Task::STATUS_ACTIVE, 'expire_date' < 'CURDATE()']
        );
        $this->actionTitle = 'Просрочено';

        return $this->render('index', [
            'provider' => $this->provider,
            'actions'  => $this->actions,
            'title'    => $this->actionTitle,
        ]);
    }
}
