<?php

namespace app\controllers;

use app\models\Tasks;
use yii\web\Controller;

class TasksController extends Controller
{
    /**
     * Отображает страницу заданий.
     *
     * @return string
     */
    public function actionIndex()
    {
        $tasks = new Tasks()->find()->all();

        return $this->render('tasks', ['tasks' => $tasks]);
    }
}
