<?php

namespace app\controllers;

use app\models\LoginForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class LandingController extends Controller
{
    public function behaviors()
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
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): array|Response|string
    {
        $this->layout = 'landing';
        $loginForm = new LoginForm();

        if (Yii::$app->request->getIsPost()) {
            $loginForm->load(Yii::$app->request->post());
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($loginForm);
            }
            if ($loginForm->validate()) {
                $user = $loginForm->getUser();
                Yii::$app->user->login($user);
                return $this->redirect(['tasks/index']);
            }
        }

        return $this->render('index', ['loginForm' => $loginForm]);
    }
}
