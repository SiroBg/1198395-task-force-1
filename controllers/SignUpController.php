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

                if ($user->save(false)) {
                    return $this->goHome();
                }
            }
        }
        return $this->render('index', [
            'user' => $user,
            'cities' => $cities,
        ]);
    }
}
