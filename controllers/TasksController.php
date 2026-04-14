<?php

namespace app\controllers;

use app\models\Categories;
use app\models\Responds;
use app\models\TaskFiles;
use app\models\Tasks;
use app\models\TasksForm;
use app\models\Users;
use DateInterval;
use DateTime;
use TaskForce\Actions\CancelAction;
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
        $taskModel = Tasks::findOne($id);

        if ($taskModel === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        $task = new Task(
            $taskModel->author_id,
            $taskModel->status,
            $taskModel->executor_id
        );

        $user = Users::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id]
        )->one();


        $responds = [];

        if ($user->is_executor) {
            $responds = Responds::find()->where(
                ['executor_id' => $user->id, 'task_id' => $taskModel->id]
            )->all();
        } elseif ($taskModel->author_id === $user->id) {
            $responds = Responds::find()->where(['task_id' => $taskModel->id])
                ->all();
        }

        $taskFiles = TaskFiles::find()->where(['task_id' => $taskModel->id])
            ->all();

        return $this->render(
            'view',
            [
                'taskModel' => $taskModel,
                'task'      => $task,
                'responds'  => $responds,
                'taskFiles' => $taskFiles,
                'user'      => $user,
            ],
        );
    }

    public function actionCancel($taskId): \yii\web\Response
    {
        if (CancelAction::checkRights()) {
            return $this->redirect(['view', 'id' => $taskId]);
        }
    }

    public function actionFinish($taskId)
    {
        $task = Task::findOne($taskId);
        $review = new Review();

        $review->task_id = $taskId;
        $review->customer_id = $task->customer_id;
        $review->performer_id = $task->performer_id;

        Offer::updateAll(
            ['status' => OfferStatus::COMPLETED->value],
            [
                'and',
                ['task_id' => $task->id],
                ['status' => OfferStatus::CONFIRM->value],
            ]
        );

        $task->status = TaskStatus::COMPLETE->value;
        $task->save(false);


        if ($review->load(Yii::$app->request->post()) && $review->validate()) {
            if ($review->save()) {
                Yii::$app->session->setFlash('success', 'Задание закрыто');
                return $this->redirect(['task/view', 'id' => $taskId]);
            } else {
                Yii::error($review->errors);
            }
        }

        return $this->redirect(['view', 'id' => $taskId]);
    }

    public function actionRespond($taskId)
    {
        $newOffer = new Offer();

        $newOffer->task_id = $taskId;
        $newOffer->performer_id = Yii::$app->user->id;
        $newOffer->status = OfferStatus::NEW->value;

        if ($newOffer->load(Yii::$app->request->post())
            && $newOffer->validate()
        ) {
            if ($newOffer->save()) {
                Yii::$app->session->setFlash('success', 'Офер создан');
                return $this->redirect(['task/view', 'id' => $taskId]);
            } else {
                Yii::error($newOffer->errors);
            }
        }

        if (!$newOffer->save()) {
            var_dump($newOffer->errors);
        }
        exit;

        return $this->redirect(['task/view', 'id' => $taskId]);
    }

    public function actionStart($taskId, $offerId)
    {
        $task = Task::findOne($taskId);
        $taskService = new TaskService;

        if (!$task
            || !$taskService->accept(
                Yii::$app->user->id,
                $taskId,
                $offerId
            )
        ) {
            throw new NotFoundHttpException();
        }

        return $this->redirect(['view', 'id' => $taskId]);
    }

    public function actionReject($taskId, $offerId)
    {
        $taskService = new TaskService;

        if (!$taskService->reject(Yii::$app->user->id, $taskId, $offerId)) {
            Yii::$app->session->setFlash('error', 'Нельзя отказать');
        }

        return $this->redirect(['view', 'id' => $taskId]);
    }

    public function actionRefuse($taskId)
    {
        $task = Task::findOne($taskId);

        Offer::updateAll(
            ['status' => OfferStatus::FAILED->value],
            [
                'and',
                ['task_id' => $task->id],
                ['status' => OfferStatus::CONFIRM->value],
            ]
        );

        $task->status = TaskStatus::FAILED->value;
        $task->save(false);

        return $this->redirect(['view', 'id' => $taskId]);
    }
}
