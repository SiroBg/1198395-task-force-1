<?php

/**
 * @var $user
 * @var $cities
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<main class="container container--registration">
    <div class="center-block">
        <div class="registration-form regular-form">
            <?php
            $form = ActiveForm::begin(
                ['enableAjaxValidation' => true, 'method' => 'post'],
            ); ?>
            <h3 class="head-main head-task">Регистрация нового пользователя</h3>
            <?= $form->field($user, 'name')
                ->textInput(
                    ['labelOptions' => ['class' => 'control-label']],
                ) ?>
            <div class="half-wrapper">
                <?= $form->field($user, 'email')->input(
                    'email',
                    ['labelOptions' => ['class' => 'control-label']],
                ) ?>
                <?= $form->field($user, 'city_id')->dropDownList(
                    ArrayHelper::map($cities, 'id', 'name'),
                ) ?>

            </div>
            <div class="half-wrapper">
                <?= $form->field($user, 'password')->passwordInput(
                    ['labelOptions' => ['class' => 'control-label']],
                ) ?>
            </div>
            <div class="half-wrapper">
                <?= $form->field($user, 'password_retype')->passwordInput(
                    ['labelOptions' => ['class' => 'control-label']],
                ) ?>
            </div>
            <?= $form->field($user, 'is_executor')->checkbox(
                [
                    'labelOptions' => ['class' => 'control-label checkbox-label'],
                    'checked' => true,
                ],
            ) ?>
            <?= Html::submitInput('Создать аккаунт', ['class' => 'button button--blue']); ?>

            <?php
            ActiveForm::end(); ?>
        </div>
    </div>
</main>
