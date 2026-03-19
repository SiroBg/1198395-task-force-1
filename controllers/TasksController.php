<?php

namespace app\controllers;

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
        return $this->render('tasks');
    }
}
