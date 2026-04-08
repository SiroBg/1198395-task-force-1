<?php

namespace app\controllers;

use app\models\Cities;
use app\models\Users;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class SignUpController extends Controller
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
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): array|Response|string
    {
        $cities = Cities::find()->select(['id', 'name'])->all();

        $user = new Users();
        if (Yii::$app->request->getIsPost()) {
            $user->load(Yii::$app->request->post());
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($user);
            }
            if ($user->validate()) {
                $user->password = Yii::$app->security->generatePasswordHash(
                    $user->password
                );

                if ($user->save(false)) {
                    return $this->goHome();
                }
            }
        }
        return $this->render('index', [
            'user'   => $user,
            'cities' => $cities,
        ]);
    }
}
