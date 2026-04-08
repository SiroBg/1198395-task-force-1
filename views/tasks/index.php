<?php

/**
 * @var \app\models\Tasks      $tasks      ;
 * @var \app\models\Categories $categories ;
 * @var TasksForm              $tasksFrom  ;
 */

use app\models\TasksForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main head-task">Новые задания</h3>
        <?php
        foreach ($tasks as $task) : ?>
            <div class="task-card">
                <div class="header-task">
                    <a href="/tasks/view/<?= $task->id; ?>"
                       class="link link--block link--big"><?= Html::encode(
                            $task->name,
                        ); ?></a>
                    <p class="price price--task"><?= $task->budget; ?> ₽</p>
                </div>
                <p class="info-text"><span class="current-time">
                <?= Yii::$app->formatter->asRelativeTime($task->created_at); ?>
            </span>
                </p>
                <p class="task-text"><?= Html::encode(
                        $task->description,
                    ); ?></p>
                <div class="footer-task">
                    <p class="info-text town-text"><?= $task->city->name ??
                            'Абаза'; ?></p>
                    <p class="info-text category-text"><?= $task->category->name; ?></p>
                    <a href="#" class="button button--black">Смотреть
                        Задание</a>
                </div>
            </div>
        <?php
        endforeach; ?>
        <?php
        if ($pagination->pageCount > 1): ?>
            <?= LinkPager::widget([
                'pagination'           => $pagination,
                'options'              => ['class' => 'pagination-list'],
                'linkContainerOptions' => ['class' => 'pagination-item'],
                'linkOptions'          => ['class' => 'link link--page'],
                'activePageCssClass'   => 'pagination-item--active',
                'disabledPageCssClass' => 'mark',
                'prevPageLabel'        => '',
                'nextPageLabel'        => '',
                'prevPageCssClass'     => 'pagination-item mark',
                'nextPageCssClass'     => 'pagination-item mark',
                'maxButtonCount'       => 3,
            ]) ?>
        <?php
        endif; ?>
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
                    <?= $form->field($tasksForm, 'categories')
                        ->checkboxList(
                            ArrayHelper::map($categories, 'id', 'name'),
                            [
                                'separator' => '<br>',
                                'class'     => 'control-label',
                            ],
                        )->error(['tag' => false])->label(false); ?>
                </div>
                <h4 class="head-card">Дополнительно</h4>
                <?= $form->field($tasksForm, 'noResponds')->checkbox([
                    'labelOptions' => ['class' => 'control-label'],
                ])->error(['tag' => false]); ?>
                <h4 class="head-card">Период</h4>
                <?= $form->field($tasksForm, 'period')->dropDownList(
                    TasksForm::PERIODS_OPTIONS,
                )->label(false); ?>

                <?= Html::submitInput(
                    'Искать',
                    ['class' => 'button button--blue']
                ) ?>

                <?php
                ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</main>
