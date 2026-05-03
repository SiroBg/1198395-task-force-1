<?php

namespace app\models;

use yii\base\Model;

class LoginForm extends Model
{
    public string $email    = '';
    public string $password = '';

    private ?User $_user = null;

    /**
     * {@inheritDoc}
     * @return array
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * {@inheritDoc}
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'email'    => 'Email',
            'password' => 'Пароль',
        ];
    }

    /**
     * Валидирует пароль.
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validatePassword($attribute, $params): void
    {
        if ( ! $this->hasErrors()) {
            $user = $this->getUser();
            if ( ! $user || ! $user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неправильный email или пароль');
            }
        }
    }

    /**
     * Получает информацию о пользователе по email.
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }
}
