<?php

namespace app\controllers;

use app\models\Categories;
use app\models\Tasks;
use app\models\TasksForm;
use DateInterval;
use DateTime;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TasksController extends Controller
{
    public function behaviors()
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
     */
    public function actionIndex()
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
                $query->andWhere(['>', 'created_at', $date->format('Y-m-d H:i:s')]);
            }
        }

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        $tasks = $provider->getModels();

        return $this->render('index', ['tasks' => $tasks, 'categories' => $categories, 'tasksForm' => $tasksForm]);
    }

    public function actionView(int $id)
    {
        $task = Tasks::findOne($id);

        if ($task === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        return $this->render('view', ['task' => $task]);
    }
}
