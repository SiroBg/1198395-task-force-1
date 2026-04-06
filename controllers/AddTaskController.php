<?php

namespace app\controllers;

use app\models\Categories;
use app\models\Files;
use app\models\TaskFiles;
use app\models\Tasks;
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
                'class' => \yii\filters\AccessControl::class,
                'denyCallback' => function ($rule, $action) {
                    return Yii::$app->response->redirect(['/tasks']);
                },
                'rules' => [
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
        $categories = Categories::find()->select(['id', 'name'])->all();
        $task = new Tasks();

        if (Yii::$app->request->getIsPost()) {
            $task->load(Yii::$app->request->post());

            $task->task_files = UploadedFile::getInstances(
                $task,
                'task_files',
            );

            $task->author_id = Yii::$app->user->id;
            $task->status = $task::STATUS_STATUS_NEW;

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($task);
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                if (!$task->save() || !$this->uploadTaskFiles($task)) {
                    throw new \Exception('Ошибка при сохранении задания на сервер.');
                }

                $transaction->commit();
                return $this->goHome();
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage());
            }

        }
        return $this->render('index', [
            'task' => $task,
            'categories' => $categories,
        ]);
    }

    private function uploadTaskFiles(Tasks $task)
    {
        $success = true;

        exit('Доходит');

        if (!empty($task->task_files)) {
            foreach ($task->task_files as $file) {
                $fileName = uniqid() . '.' . $file->extension;
                $file->saveAs('@webroot/uploads/' . $fileName);

                $newFile = new Files();
                $newFile->file_path = $fileName;
                $newFile->url = Yii::getAlias('@webroot/uploads/')
                    . $fileName;

                if ($newFile->save()) {
                    $taskFile = new TaskFiles();
                    $taskFile->task_id = $task->id;
                    $taskFile->file_id = $newFile->id;
                    var_dump($task->id . ' ' . $newFile->id);
                    exit();
                    $success = $taskFile->save();
                } else {
                    $success = false;
                }
            }
        }

        return $success;
    }
}
