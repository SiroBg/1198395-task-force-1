<?php

namespace app\controllers;

use app\models\Task;
use app\models\User;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
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
     * Отображает страницу исполнителя.
     * @param  int  $id  ID исполнителя.
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $user = User::findOne($id);

        if ($user === null || ! $user->is_executor) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        $userReviews  = $user->reviewsAsExecutor;
        $showContacts = $this->showContacts($user);

        return $this->render('view', [
            'user'         => $user,
            'userReviews'  => $userReviews,
            'showContacts' => $showContacts
        ]);
    }

    /**
     * Выполняет выход пользователя из приложения.
     * @return \yii\web\Response
     */
    public function actionLogout(): \yii\web\Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Вычисляет, нужно ли отображать контакты исполнителя.
     * @param  User  $user  Исполнитель.
     * @return bool
     */
    private function showContacts(User $user): bool
    {
        $currentUser = User::findOne(Yii::$app->user->id);
        $userTasks   = [];
        if ( ! $currentUser->is_executor) {
            $userTasks = Task::find()
                             ->where([
                                 'author_id'   => $currentUser->id,
                                 'executor_id' => $user->id,
                             ])->all();
        }

        return match (true) {
            $currentUser->id === $user->id => true,
            count($userTasks) > 0          => true,
            $user->show_contacts === true  => true,
            default                        => false,
        };
    }
}
