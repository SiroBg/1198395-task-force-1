<?php

namespace app\controllers;

use app\models\Categories;
use app\models\Responds;
use app\models\TaskFiles;
use app\models\Tasks;
use app\models\TasksForm;
use DateInterval;
use DateTime;
use TaskForce\Task;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TasksController extends Controller
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
     * Отображает страницу заданий.
     *
     * @return string
     * @throws \DateMalformedIntervalStringException
     */
    public function actionIndex(): string
    {
        $query = Tasks::find()->where(['status' => Tasks::STATUS_STATUS_NEW]);

        $categories = Categories::find()->select(['id', 'name'])->all();

        $tasksForm = new TasksForm();

        if ($tasksForm->load(Yii::$app->request->get())) {
            if (!empty($tasksForm->categories)) {
                $query->andWhere(['category_id' => $tasksForm->categories]);
            }

            if ($tasksForm->noResponds) {
                $query->andWhere(['executor_id' => null]);
            }

            if (!empty($tasksForm->period) && $tasksForm->validate()) {
                $interval = new DateInterval($tasksForm->period);

                $date = date_sub(new DateTime(), $interval);
                $query->andWhere(
                    ['>', 'created_at', $date->format('Y-m-d H:i:s')],
                );
            }
        }

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        $tasks = $provider->getModels();
        $pagination = $provider->pagination;

        return $this->render(
            'index',
            [
                'tasks' => $tasks,
                'categories' => $categories,
                'tasksForm' => $tasksForm,
                'pagination' => $pagination,
            ],
        );
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $taskModel = Tasks::findOne($id);

        if ($taskModel === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        $task = new Task($taskModel->author_id, $taskModel->status, $taskModel->executor_id);
        $responds = Responds::find()->where(['task_id' => $taskModel->id])->all();
        $taskFiles = TaskFiles::find()->where(['task_id' => $taskModel->id])->all();

        return $this->render(
            'view',
            [
                'taskModel' => $taskModel,
                'task' => $task,
                'responds' => $responds,
                'taskFiles' => $taskFiles,
            ],
        );
    }
}
