<?php

namespace app\controllers;

use app\models\City;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class SignUpController extends Controller
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
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Отображает страницу регистрации.
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function actionIndex(): array|Response|string
    {
        $cities = City::find()->select(['id', 'name'])->all();

        $user           = new User();
        $user->scenario = 'signup';
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
                $user->scenario = 'default';
                if ($user->save()) {
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
