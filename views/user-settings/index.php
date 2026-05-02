<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var \app\models\User     $user           ;
 * @var \app\models\Category $categories     ;
 * @var array                $userCategories ;
 */

?>

<main class="main-content main-content--left container">
    <div class="left-menu left-menu--edit">
        <h3 class="head-main head-task">Настройки</h3>
        <ul class="side-menu-list">
            <li class="side-menu-item side-menu-item--active">
                <a class="link link--nav">Мой профиль</a>
            </li>
            <li class="side-menu-item">
                <?= Html::a(
                    'Безопасность',
                    ['/user-settings/security'], ['class' => 'link link--nav']
                ) ?>
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
        <h3 class="head-main head-regular">Мой профиль</h3>
        <div class="photo-editing">
            <div>
                <p class="form-label">Аватар</p>
                <img class="avatar-preview" src="
                <?= $user->profileImgFile->url ?? './img/avatar-placeholder.png'; ?>"
                     width="83" height="83" alt="Превью аватарки">
            </div>
            <?= $form->field(
                $user,
                'avatar',
            )
                     ->fileInput([
                         'multiple' => false,
                         'style'    => 'display:none',
                     ])->label('Сменить аватар', ['class' => 'button button--black']); ?>
        </div>
        <?= $form->field($user, 'name')
                 ->textInput(
                     ['labelOptions' => ['class' => 'control-label']],
                 ) ?>
        <div class="half-wrapper">
            <?= $form->field($user, 'email')->input(
                'email',
                ['labelOptions' => ['class' => 'control-label']],
            ) ?>
            <?= $form->field($user, 'birthday')
                     ->input(
                         'date',
                         ['labelOptions' => ['class' => 'control-label']],
                     ) ?>
        </div>
        <div class="half-wrapper">
            <?= $form->field($user, 'phone')->input(
                'tel',
                ['labelOptions' => ['class' => 'control-label']],
            ) ?>
            <?= $form->field($user, 'telegram')
                     ->textInput(['labelOptions' => ['class' => 'control-label']]) ?>
        </div>
        <?= $form->field($user, 'about')
                 ->textarea(
                     ['labelOptions' => ['class' => 'control-label']],
                 ) ?>
        <div class="form-group">
            <p class="form-label">Выбор специализаций</p>
            <?= $form->field($user, 'categories')
                     ->checkboxList(
                         ArrayHelper::map($categories, 'id', 'name'),
                         [
                             'class' => 'checkbox-profile',
                             'item'  => function ($index, $label, $name, $checked, $value) {
                                 $check = $checked ? 'checked' : '';
                                 return "<label class='control-label'>
                                                <input type='checkbox' name='$name' value='$value' $check> $label
                                            </label>";
                             }
                         ],
                     )->error(['tag' => false])->label(false); ?>
        </div>
        <?= Html::submitInput(
            'Сохранить',
            ['class' => 'button button--blue']
        ); ?>
        <?php
        ActiveForm::end(); ?>
    </div>
</main>