<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var \app\models\SettingsSecurityForm $settingsForm ;
 */

?>

<main class="main-content main-content--left container">
    <div class="left-menu left-menu--edit">
        <h3 class="head-main head-task">Настройки</h3>
        <ul class="side-menu-list">
            <li class="side-menu-item">
                <?= Html::a(
                    'Мой профиль',
                    ['/user-settings/'], ['class' => 'link link--nav']
                ) ?>
            </li>
            <li class="side-menu-item side-menu-item--active">
                <a class="link link--nav">Безопасность</a>
            </li>
        </ul>
    </div>
    <div class="my-profile-form">
        <?php
        $form = ActiveForm::begin(
            [
                'enableAjaxValidation' => true,
                'method'               => 'post',
            ],
        ); ?>
        <h3 class="head-main head-regular">Безопасность</h3>
        <?= $form->field($settingsForm, 'old_password')->passwordInput(
            ['labelOptions' => ['class' => 'control-label']],
        ) ?>
        <?= $form->field($settingsForm, 'new_password')->passwordInput(
            ['labelOptions' => ['class' => 'control-label']]
        ) ?>
        <?= $form->field($settingsForm, 'password_retype')->passwordInput(
            ['labelOptions' => ['class' => 'control-label']],
        ) ?>
        <?= $form->field($settingsForm, 'show_contacts')->checkbox(
            [
                'labelOptions' => ['class' => 'control-label checkbox-label'],
                'checked'      => $settingsForm->show_contacts,
            ],
        ) ?>
        <?= Html::submitInput(
            'Сохранить',
            ['class' => 'button button--blue']
        ); ?>
        <?php
        ActiveForm::end(); ?>
    </div>
</main>