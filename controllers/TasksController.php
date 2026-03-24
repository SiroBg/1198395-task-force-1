<?php

namespace app\controllers;

use app\models\Tasks;
use yii\data\ActiveDataProvider;
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
        $query = Tasks::find()->where(['status' => Tasks::STATUS_STATUS_NEW]);

        $provider = new ActiveDataProvider([
            'query' => $query,
                'pagination' => [
            'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        $tasks = $provider->getModels();

        return $this->render('tasks', ['tasks' => $tasks]);
    }
}
