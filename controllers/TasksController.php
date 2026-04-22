<?php

namespace app\controllers;

use app\actions\actionCancel;
use app\actions\actionFinish;
use app\actions\actionRefuse;
use app\actions\actionRespond;
use app\actions\actionStart;
use app\models\Categories;
use app\models\Responds;
use app\models\Reviews;
use app\models\TaskFiles;
use app\models\Tasks;
use app\models\TasksForm;
use app\models\Users;
use DateInterval;
use DateTime;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
        $query = Tasks::find()->where(['status' => Tasks::STATUS_NEW]);

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
        $task = Tasks::findOne($id);

        if ($task === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        $responds = [];
        $hasResponds = false;

        if ($user->is_executor) {
            $responds = Responds::find()->where(
                ['executor_id' => $user->id, 'task_id' => $task->id],
            )->all();
            $hasResponds = in_array($user->id, array_column($responds, 'executor_id')) && $task->executor_id !== $user->id;
        } elseif ($task->author_id === $user->id) {
            $responds = Responds::find()->where(['task_id' => $task->id])
                ->all();
        }

        $taskFiles = TaskFiles::find()->where(['task_id' => $task->id])
            ->all();

        $reviewForm = new Reviews();
        $respondForm = new Responds();

        return $this->render(
            'view',
            [
                'task' => $task,
                'responds' => $responds,
                'taskFiles' => $taskFiles,
                'user' => $user,
                'reviewForm' => $reviewForm,
                'respondForm' => $respondForm,
                'hasResponds' => $hasResponds,
            ],
        );
    }

    public function actionCancel(int $taskId): Response
    {
        $task = Tasks::find()->where(['id' => $taskId])->one();
        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if (!actionCancel::execute($task, $user)) {
            throw new Exception('Не удалось загрузить данные на сервер');
        }

        return $this->redirect(['view', 'id' => $taskId]);
    }

    public function actionFinish(int $taskId): array|Response
    {
        $task = Tasks::find()->where(['id' => $taskId])->one();

        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if (!actionFinish::execute($task, $user)) {
            throw new Exception('Не удалось загрузить данные на сервер');
        }

        return $this->redirect(['view', 'id' => $task->id]);
    }

    public function actionRespond(int $taskId): array|Response
    {

        $task = Tasks::find()->where(['id' => $taskId])->one();

        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if (!actionRespond::execute($task, $user)) {
            throw new Exception('Не удалось загрузить данные на сервер');
        }

        return $this->redirect(['view', 'id' => $task->id]);
    }

    public function actionStart($taskId, $executorId): Response
    {
        $task = Tasks::find()->where(['id' => $taskId])->one();
        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if (!actionStart::execute($task, $user, $executorId)) {
            throw new Exception('Не удалось загрузить данные на сервер');
        }

        return $this->redirect(['view', 'id' => $task->id]);
    }

    public function actionReject(int $taskId, int $respondId): Response
    {
        $task = Tasks::find()->where(['id' => $taskId])->one();
        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if ($task->author_id === $user->id) {
            Responds::updateAll(['rejected' => true], ['id' => $respondId]);
        }

        return $this->redirect(['view', 'id' => $taskId]);
    }

    public function actionRefuse($taskId): Response
    {
        $task = Tasks::find()->where(['id' => $taskId])->one();

        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if (!actionRefuse::execute($task, $user)) {
            throw new Exception('Не удалось загрузить данные на сервер');
        }

        return $this->redirect(['view', 'id' => $task->id]);
    }
}
