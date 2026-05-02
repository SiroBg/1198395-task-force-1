<?php

namespace app\controllers;

use app\models\Category;
use app\models\File;
use app\models\SettingsSecurityForm;
use app\models\User;
use app\models\UserCategory;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

class UserSettingsController extends Controller
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
        $user             = User::findOne(Yii::$app->user->id);
        $categories       = Category::find()->select(['id', 'name'])->all();
        $userCategories   = UserCategory::find()->where(['user_id' => $user->id])
                                        ->select(['category_id'])->asArray()->all();
        $user->categories = array_map(function ($userCategories) {
            return $userCategories['category_id'];
        }, $userCategories);

        if (Yii::$app->request->getIsPost()) {
            $user->load(Yii::$app->request->post());

            $user->avatar = UploadedFile::getInstance(
                $user,
                'avatar',
            );

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($user);
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $avatarFile = $this->uploadUserAvatar($user);
                if ($avatarFile && $avatarFile->save()) {
                    $user->profile_img_file_id = $avatarFile->id;
                    $user->avatar              = null;
                }
                if ($this->updateUserCategories($user) && $user->save()) {
                    $transaction->commit();
                    return $this->redirect(['/users/view/'.$user->id]);
                } else {
                    throw new Exception('Не удалось обновить данные о пользователе.');
                }
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw new Exception($e->getMessage());
            }
        }

        return $this->render(
            'index',
            ['user' => $user, 'categories' => $categories]
        );
    }

    /**
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function actionSecurity(): array|Response|string
    {
        $user                        = User::findOne(Yii::$app->user->id);
        $settingsForm                = new SettingsSecurityForm();
        $settingsForm->show_contacts = $user->show_contacts;

        if (Yii::$app->request->getIsPost()) {
            $settingsForm->load(Yii::$app->request->post());

            if ( ! empty($settingsForm->old_password)) {
                $settingsForm->scenario = 'passwordChange';
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($settingsForm);
            }

            if ($settingsForm->validate()) {
                if ($settingsForm->scenario === 'passwordChange') {
                    $user->password = Yii::$app->getSecurity()
                                               ->generatePasswordHash($settingsForm->new_password);
                }
                $user->show_contacts = $settingsForm->show_contacts;
                if ($user->save()) {
                    return $this->redirect(['/users/view/'.$user->id]);
                } else {
                    throw new Exception('Не удалось обновить данные');
                }
            }
        }

        return $this->render('security', ['settingsForm' => $settingsForm]);
    }

    private function uploadUserAvatar(User $user): null|File
    {
        $result = null;

        if ($user->avatar) {
            $fileName = uniqid().'.'.$user->avatar->extension;
            $user->avatar->saveAs('@webroot/uploads/'.$fileName);

            $newFile            = new File();
            $newFile->file_path = Yii::getAlias('@webroot/uploads/')
                                  .$fileName;
            $newFile->url       = '/uploads/'.$fileName;
            $newFile->name      = $user->avatar->name;
            $result             = $newFile;
        }
        return $result;
    }

    public function updateUserCategories(User $user): bool
    {
        $success = true;
        UserCategory::deleteAll(['user_id' => $user->id]);

        if ( ! empty($user->categories)) {
            foreach ($user->categories as $categoryId) {
                $userCategory              = new UserCategory();
                $userCategory->user_id     = $user->id;
                $userCategory->category_id = $categoryId;

                if ( ! $userCategory->save()) {
                    $success = false;
                    break;
                }
            }
        }

        return $success;
    }
}
