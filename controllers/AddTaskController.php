<?php

namespace app\controllers;

use app\models\Categories;
use app\models\Cities;
use app\models\Files;
use app\models\TaskFiles;
use app\models\Tasks;
use app\models\Users;
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
        $categories = Categories::find()->select(['id', 'name'])->all();

        $task = new Tasks();

        if (Yii::$app->request->getIsPost()) {
            $task->load(Yii::$app->request->post());

            $task->task_files = UploadedFile::getInstances(
                $task,
                'task_files'
            );

            $task->author_id = Yii::$app->user->id;
            $task->status = $task::STATUS_STATUS_NEW;

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($task);
            }

            if ($task->validate()) {
                if ($task->save()) {
                    $task->upload();
                    return $this->goHome();
                }
            }
        }
        return $this->render('index', [
            'task'       => $task,
            'categories' => $categories,
        ]);
    }
}
