<?php

namespace app\controllers;

use app\models\Category;
use app\models\File;
use app\models\Task;
use app\models\TaskFile;
use app\models\User;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

class AddTaskController extends Controller
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * Отображает страницу добавления задания.
     * @return array|Response|string
     * @throws Exception
     */
    public function actionIndex(): array|Response|string
    {
        $user = User::find()->where(
            ['id' => Yii::$app->user->id],
        )->one();

        if ($user->is_executor) {
            $this->redirect('/');
        }

        $userCityName = $user->city->name;

        $categories = Category::find()->select(['id', 'name'])->all();

        $task = new Task();

        if (Yii::$app->request->getIsPost()) {
            $task->load(Yii::$app->request->post());

            $task->task_files = UploadedFile::getInstances(
                $task,
                'task_files',
            );

            $task->author_id = $user->id;
            $task->status    = $task::STATUS_NEW;
            $task->scenario  = 'create';

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($task);
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ( ! $task->save() || ! $this->uploadTaskFiles($task)) {
                    throw new \Exception(
                        'Ошибка при сохранении задания на сервер.',
                    );
                }

                $transaction->commit();
                return $this->goHome();
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw new Exception($e->getMessage());
            }
        }

        return $this->render('index', [
            'task'         => $task,
            'categories'   => $categories,
            'userCityName' => $userCityName,
        ]);
    }

    /**
     * Загружает файлы задания.
     * @param  Task  $task  Задание.
     * @return bool Успех/провал добавления файлов задания.
     * @throws Exception
     */
    private function uploadTaskFiles(Task $task): bool
    {
        $success = true;

        if ( ! empty($task->task_files)) {
            foreach ($task->task_files as $file) {
                $fileName = uniqid().'.'.$file->extension;
                $file->saveAs('@webroot/uploads/'.$fileName);

                $newFile            = new File();
                $newFile->file_path = Yii::getAlias('@webroot/uploads/')
                                      .$fileName;
                $newFile->url       = '/uploads/'.$fileName;
                $newFile->name      = $file->name;

                if ($newFile->save()) {
                    $taskFile          = new TaskFile();
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
