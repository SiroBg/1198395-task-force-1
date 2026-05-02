<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SettingsSecurityForm extends Model
{
    public string $old_password    = '';
    public string $new_password    = '';
    public string $password_retype = '';
    public bool   $show_contacts   = true;

    public function rules(): array
    {
        return [
            [
                ['old_password', 'new_password', 'password_retype'],
                'required',
                'on' => 'passwordChange'
            ],
            ['show_contacts', 'boolean'],
            ['new_password', 'string', 'min' => 8, 'max' => 128],
            ['old_password', 'validatePassword'],
            [
                'password_retype',
                'compare',
                'compareAttribute' => 'new_password',
                'message'          => 'Пароли не совпадают',
            ]
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'old_password'    => 'Предыдущий пароль',
            'new_password'    => 'Новый пароль',
            'password_retype' => 'Повторите пароль',
            'show_contacts'   => 'Показывать контакты всем'
        ];
    }

    public function validatePassword($attribute, $params): void
    {
        $user = User::findOne(Yii::$app->user->id);
        if ( ! $user || ! $user->validatePassword($this->old_password)) {
            $this->addError($attribute, 'Неправильный пароль');
        }
    }
}
