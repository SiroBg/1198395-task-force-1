<?php

namespace app\controllers;

use app\models\Category;
use app\models\File;
use app\models\TaskFile;
use app\models\Task;
use app\models\User;
use app\validators\LocationValidator;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

class AddTaskController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class'        => \yii\filters\AccessControl::class,
                'denyCallback' => function ($rule, $action) {
                    return Yii::$app->response->redirect(['/tasks']);
                },
                'rules'        => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): array|Response|string
    {
        $user = User::find()->select(['id', 'is_executor'])->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if ($user->is_executor) {
            $this->redirect('/');
        }

        $categories = Category::find()->select(['id', 'name'])->all();

        $task = new Task();

        if (Yii::$app->request->getIsPost()) {
            $task->load(Yii::$app->request->post());

            $task->task_files = UploadedFile::getInstances(
                $task,
                'task_files',
            );

            $task->author_id = $user->id;
            $task->status = $task::STATUS_NEW;

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($task);
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                [$long, $lat] = Yii::$app->yandexGeocoder->getCoordinates(
                    $task->location
                );

                $task->long = $long;
                $task->lat = $lat;

                if (!$task->save() || !$this->uploadTaskFiles($task)) {
                    throw new \Exception(
                        'Ошибка при сохранении задания на сервер.',
                    );
                }

                $transaction->commit();
                return $this->goHome();
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage());
            }
        }
        return $this->render('index', [
            'task'       => $task,
            'categories' => $categories,
        ]);
    }

    private function uploadTaskFiles(Task $task): bool
    {
        $success = true;

        if (!empty($task->task_files)) {
            foreach ($task->task_files as $file) {
                $fileName = uniqid() . '.' . $file->extension;
                $file->saveAs('@webroot/uploads/' . $fileName);

                $newFile = new File();
                $newFile->file_path = Yii::getAlias('@webroot/uploads/')
                    . $fileName;
                $newFile->url = '/uploads/' . $fileName;
                $newFile->name = $file->name;

                if ($newFile->save()) {
                    $taskFile = new TaskFile();
                    $taskFile->task_id = $task->id;
                    $taskFile->file_id = $newFile->id;

                    $success = $taskFile->save();
                } else {
                    $success = false;
                }
            }
        }

        return $success;
    }
}
