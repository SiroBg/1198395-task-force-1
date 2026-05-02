<?php

namespace app\controllers;

use app\actions\ActionCancel;
use app\actions\ActionFinish;
use app\actions\ActionRefuse;
use app\actions\ActionRespond;
use app\actions\ActionStart;
use app\models\Category;
use app\models\Respond;
use app\models\Review;
use app\models\TaskFile;
use app\models\Task;
use app\models\TaskForm;
use app\models\TaskSearch;
use app\models\User;
use DateInterval;
use DateTime;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
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
        $categories = Category::find()->select(['id', 'name'])->all();

        $taskSearch = new TaskSearch();
        $provider   = $taskSearch->getNewTasksProvider();

        if ($taskSearch->load(Yii::$app->request->get())) {
            $provider = $taskSearch->getFilteredProvider();
        }

        return $this->render(
            'index',
            [
                'provider'   => $provider,
                'categories' => $categories,
                'taskSearch' => $taskSearch,
            ],
        );
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $task = Task::findOne($id);

        if ($task === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        $user = User::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        $responds    = [];
        $hasResponds = false;

        if ($user->is_executor) {
            $responds    = Respond::find()->where(
                ['executor_id' => $user->id, 'task_id' => $task->id],
            )->all();
            $hasResponds = in_array(
                               $user->id,
                               array_column($responds, 'executor_id')
                           )
                           && $task->executor_id !== $user->id;
        } elseif ($task->author_id === $user->id) {
            $responds = Respond::find()->where(['task_id' => $task->id])
                               ->all();
        }

        $taskFiles = TaskFile::find()->where(['task_id' => $task->id])
                             ->all();

        $reviewForm  = new Review();
        $respondForm = new Respond();

        return $this->render(
            'view',
            [
                'task'        => $task,
                'responds'    => $responds,
                'taskFiles'   => $taskFiles,
                'user'        => $user,
                'reviewForm'  => $reviewForm,
                'respondForm' => $respondForm,
                'hasResponds' => $hasResponds,
            ],
        );
    }

    public function actionCancel(int $taskId): Response
    {
        $task = Task::find()->where(['id' => $taskId])->one();
        $user = User::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        try {
            new ActionCancel()->applyAction($task, $user);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }

        return $this->redirect(['view', 'id' => $taskId]);
    }

    /**
     * @throws Exception
     */
    public function actionFinish(int $taskId): array|Response
    {
        $task = Task::find()->where(['id' => $taskId])->one();

        $user = User::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        try {
            new ActionFinish()->applyAction($task, $user);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }

        return $this->redirect(['view', 'id' => $task->id]);
    }

    public function actionRespond(int $taskId): array|Response
    {
        $task = Task::find()->where(['id' => $taskId])->one();

        $user = User::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        try {
            new ActionRespond()->applyAction($task, $user);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }

        return $this->redirect(['view', 'id' => $task->id]);
    }

    public function actionStart($taskId, $executorId): Response
    {
        $task = Task::find()->where(['id' => $taskId])->one();
        $user = User::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        try {
            new ActionStart()->applyAction($task, $user, $executorId);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }

        return $this->redirect(['view', 'id' => $task->id]);
    }

    public function actionReject(int $taskId, int $respondId): Response
    {
        $task = Task::find()->where(['id' => $taskId])->one();
        $user = User::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if ($task->author_id === $user->id) {
            Respond::updateAll(['rejected' => true], ['id' => $respondId]);
        }

        return $this->redirect(['view', 'id' => $taskId]);
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionRefuse($taskId): Response
    {
        $task = Task::find()->where(['id' => $taskId])->one();

        $user = User::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        try {
            new ActionRefuse()->applyAction($task, $user);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }

        return $this->redirect(['view', 'id' => $task->id]);
    }
}
