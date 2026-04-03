<?php

/**
 * $user
 * $cities
 */

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

?>
<main class="container container--registration">
    <div class="center-block">
        <div class="registration-form regular-form">
             <?php $form = ActiveForm::begin(['enableAjaxValidation' => true, 'method' => 'post']); ?>
                <h3 class="head-main head-task">Регистрация нового пользователя</h3>
                <div class="form-group">
                     <?= $form->field($user, 'name')
                    ->textInput(['labelOptions' => ['class' => 'control-label']]) ?>
                </div>
                <div class="half-wrapper">
                    <div class="form-group">
                    <?= $form->field($user, 'email')->input('email', ['labelOptions' => ['class' => 'control-label']]) ?>
                    </div>
                    <div class="form-group">
                    <?= $form->field($user, 'city_id')->dropDownList(ArrayHelper::map($cities, 'id', 'name')) ?>
                    </div>
                </div>
                <div class="half-wrapper">
                <div class="form-group">
                    <?= $form->field($user, 'password')->passwordInput(['labelOptions' => ['class' => 'control-label']]) ?>
                </div>
                </div>
                <div class="half-wrapper">

                <div class="form-group">
                    <?= $form->field($user, 'password_retype')->passwordInput(['labelOptions' => ['class' => 'control-label']]) ?>
                </div>
                </div>
                <div class="form-group">
                    <?= $form->field($user, 'is_executor')->checkbox(['labelOptions' => ['class' => 'control-label checkbox-label'], 'checked' => true])?>
                </div>
                <input type="submit" class="button button--blue" value="Создать аккаунт">
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</main>
