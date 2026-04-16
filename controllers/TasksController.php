<?php

namespace app\controllers;

use app\models\Categories;
use app\models\Responds;
use app\models\Reviews;
use app\models\TaskFiles;
use app\models\Tasks;
use app\models\TasksForm;
use app\models\Users;
use DateInterval;
use DateTime;
use TaskForce\Actions\CancelAction;
use TaskForce\Actions\FinishAction;
use TaskForce\Actions\RefuseAction;
use TaskForce\Actions\RespondAction;
use TaskForce\Actions\StartAction;
use TaskForce\Task;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

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
            'query'      => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort'       => [
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
                'tasks'      => $tasks,
                'categories' => $categories,
                'tasksForm'  => $tasksForm,
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

        $taskHelper = new Task(
            $task->author_id,
            $task->status,
            $task->executor_id,
            $task->id,
        );

        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        $responds = [];

        if ($user->is_executor) {
            $responds = Responds::find()->where(
                ['executor_id' => $user->id, 'task_id' => $task->id],
            )->all();
        } elseif ($task->author_id === $user->id) {
            $responds = Responds::find()->where(['task_id' => $task->id])
                ->all();
        }

        $taskFiles = TaskFiles::find()->where(['task_id' => $task->id])
            ->all();

        $review = new Reviews();
        $respond = new Responds();

        return $this->render(
            'view',
            [
                'task'       => $task,
                'taskHelper' => $taskHelper,
                'responds'   => $responds,
                'taskFiles'  => $taskFiles,
                'user'       => $user,
                'review'     => $review,
                'respond'    => $respond,
            ],
        );
    }

    public function actionCancel(int $taskId): Response
    {
        $task = Tasks::find()->where(['id' => $taskId])->one();
        $taskHelper = new Task(
            $task->author_id,
            $task->status,
            $task->executor_id,
            $task->id,
        );
        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if (!$taskHelper->applyAction(
            new CancelAction(),
            $user->id,
            $user->is_executor
        )
        ) {
            throw new ForbiddenHttpException('Невозможно отменить задание');
        }

        Tasks::updateAll(['status' => $taskHelper->status], 'id = ' . $taskId);

        return $this->redirect(['view', 'id' => $taskId]);
    }

    public function actionFinish(int $taskId): array|Response
    {
        $review = new Reviews();

        $task = Tasks::find()->where(['id' => $taskId])->one();
        $taskHelper = new Task(
            $task->author_id,
            $task->status,
            $task->executor_id,
            $task->id,
        );
        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        $review->task_id = $task->id;
        $review->author_id = $task->author_id;
        $review->executor_id = $task->executor_id;

        if (Yii::$app->request->getIsPost()) {
            $review->load(Yii::$app->request->post());

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($review);
            }

            if ($review->validate()
                && $taskHelper->applyAction(
                    new FinishAction(),
                    $user->id,
                    $user->is_executor
                )
            ) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $updatedRows = Tasks::updateAll(
                        ['status' => $taskHelper->status],
                        ['id' => $task->id]
                    );
                    if (!$review->save() || $updatedRows !== 1) {
                        throw new \Exception(
                            'Ошибка при сохранении данных на сервере.',
                        );
                    }

                    $transaction->commit();
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage());
                }
            }
        }

        return $this->redirect(['view', 'id' => $task->id]);
    }

    public function actionRespond(int $taskId): array|Response
    {
        $respond = new Responds();
        $task = Tasks::find()->where(['id' => $taskId])->one();
        $taskHelper = new Task(
            $task->author_id,
            $task->status,
            $task->executor_id,
            $task->id,
        );
        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        $respond->task_id = $taskId;
        $respond->executor_id = $user->id;

        if (Yii::$app->request->getIsPost()) {
            $respond->load(Yii::$app->request->post());

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($respond);
            }

            if ($respond->validate()
                && $taskHelper->applyAction(
                    new RespondAction(),
                    $user->id,
                    $user->is_executor
                )
            ) {
                if (!$respond->save()) {
                    throw new \Exception(
                        'Ошибка при сохранении данных на сервере.',
                    );
                }
            }
        }
        return $this->redirect(['view', 'id' => $task->id]);
    }

    public function actionStart($taskId, $executorId): Response
    {
        $task = Tasks::find()->where(['id' => $taskId])->one();
        $taskHelper = new Task(
            $task->author_id,
            $task->status,
            $task->executor_id,
            $task->id,
        );
        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if (!$taskHelper->applyAction(
            new StartAction(),
            $user->id,
            $user->is_executor,
            $executorId
        )
        ) {
            throw new ForbiddenHttpException(
                'Невозможно назначить исполнителя задания'
            );
        }

        Tasks::updateAll(
            [
                'status'      => $taskHelper->status,
                'executor_id' => $taskHelper->executorId
            ],
            ['id' => $task->id]
        );

        return $this->redirect(['view', 'id' => $task->id]);
    }

    public function actionReject(int $taskId, int $respondId): Response
    {
        Responds::updateAll(['rejected' => true], ['id' => $respondId]);

        return $this->redirect(['view', 'id' => $taskId]);
    }

    public function actionRefuse($taskId): Response
    {
        $task = Tasks::find()->where(['id' => $taskId])->one();
        $taskHelper = new Task(
            $task->author_id,
            $task->status,
            $task->executor_id,
            $task->id,
        );
        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if (!$taskHelper->applyAction(
            new RefuseAction(),
            $user->id,
            $user->is_executor
        )
        ) {
            throw new ForbiddenHttpException(
                'Невозможно отказаться от задания'
            );
        }

        Tasks::updateAll(['status' => $taskHelper->status], ['id' => $task->id]
        );

        return $this->redirect(['view', 'id' => $task->id]);
    }
}
