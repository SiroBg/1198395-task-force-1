<?php

namespace app\controllers;

use app\models\Cities;
use app\models\Users;
use Yii;
use yii\web\Controller;

class SignUpController extends Controller
{
    public function actionIndex()
    {

        $cities = Cities::find()->select(['id', 'name'])->all();

        $user = new Users();
        if (Yii::$app->request->getIsPost()) {
            $user->load(Yii::$app->request->post());
            if ($user->validate()) {
                $user->password = Yii::$app->security->generatePasswordHash($user->password);
                $user->save();

                return $this->redirect(['site/index']);
            }
        }
        return $this->render('index', [
            'user' => $user,
            'cities' => $cities,
        ]);
    }
}
