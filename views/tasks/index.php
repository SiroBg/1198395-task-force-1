<?php

/**
 * @var \app\models\Category         $categories ;
 * @var \app\models\TaskSearch       $taskSearch ;
 * @var \yii\data\ActiveDataProvider $provider   ;
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main head-task">Новые задания</h3>
        <?= \app\components\TaskWidget::widget(['provider' => $provider]); ?>
    </div>
    <div class="right-column">
        <div class="right-card black">
            <div class="search-form">
                <?php
                $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => ['tasks/index'],
                ]); ?>

                <h4 class="head-card">Категории</h4>
                <div class="checkbox-wrapper">
                    <?= $form->field($taskSearch, 'categories')
                             ->checkboxList(
                                 ArrayHelper::map($categories, 'id', 'name'),
                                 [
                                     'separator' => '<br>',
                                     'class'     => 'control-label',
                                 ],
                             )->error(['tag' => false])->label(false); ?>
                </div>
                <h4 class="head-card">Дополнительно</h4>
                <?= $form->field($taskSearch, 'noResponds')->checkbox([
                    'labelOptions' => ['class' => 'control-label'],
                ])->error(['tag' => false]); ?>
                <?= $form->field($taskSearch, 'remoteTask')->checkbox([
                    'labelOptions' => ['class' => 'control-label'],
                ])->error(['tag' => false]); ?>
                <h4 class="head-card">Период</h4>
                <?= $form->field($taskSearch, 'period')->dropDownList(
                    \app\models\TaskSearch::PERIODS_OPTIONS,
                )->label(false); ?>

                <?= Html::submitInput(
                    'Искать',
                    ['class' => 'button button--blue'],
                ) ?>

                <?php
                ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</main>
