<?php

use kartik\rating\StarRating;
use TaskForce\Task;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var \app\models\Tasks    $task      ;
 * @var TaskForce\Task       $taskHelper;
 * @var \app\models\Responds $responds  ;
 * @var \app\models\TaskFiles $taskFiles;
 * @var \app\models\Users    $user      ;
 * @var \app\models\Reviews $review     ;
 * @var \app\models\Responds $respond   ;
 */

?>

<main class="main-content container">
    <div class="left-column">
        <div class="head-wrapper">
            <h3 class="head-main"><?= Html::encode($task->name); ?></h3>
            <?php
            if ($task->budget): ?>
                <p class="price price--big"><?= Html::encode(
                    $task->budget,
                ); ?>₽</p>
            <?php
            endif; ?>
        </div>
        <p class="task-description"><?= Html::encode(
            $task->description,
        ); ?></p>
        <?php
        foreach (
            $taskHelper->getActions(
                $user->id,
                $user->is_executor,
            ) as $action
        ): ?>
            <?php if (
                $action->getName() !== 'action_start'
                || ($action->getName() === 'act_response'
                && !array_any($responds, function ($respond) use ($user) {return $respond->executor->id === $user->id;}))
            ): ?>
            <?= Html::a(
                $action->getDescription(),
                options: [
                            'class' => 'button button--'
                                . $action->getButtonColor()
                                . ' action-btn',
                            'data-action' => $action->getName(),
                        ],
            ); ?>
            <?php endif; ?>
        <?php
        endforeach; ?>
        <?php
        if ($task->location && $task->city) : ?>
            <div class="task-map">
                <img class="map" src="img/map.png" width="725" height="346"
                     alt="<?= Html::encode($task->location); ?>">
                <p class="map-address town"><?= $task->city->name; ?></p>
                <p class="map-address"><?= Html::encode(
                    $task->location,
                ); ?></p>
            </div>
        <?php
        endif; ?>
        <?php
        if (!empty($responds)): ?>
            <h4 class="head-regular">
                <?= $user->is_executor ? 'Ваш отклик' : 'Отклики на задание'; ?>
            </h4>
            <?php
            foreach ($responds as $respond): ?>
                <div class="response-card">
                    <img class="customer-photo" src="img/man-glasses.png"
                         width="146"
                         height="156" alt="Фото заказчиков">
                    <div class="feedback-wrapper">
                        <a href="<?= Url::toRoute(
                            ['users/view', 'id' => $respond->executor->id],
                        ); ?>"
                           class="link link--block link--big"><?= Html::encode(
                               $respond->executor->name,
                           ); ?></a>
                        <div class="response-wrapper">
                            <div class="stars-rating small"><span
                                        class="fill-star">&nbsp;</span><span
                                        class="fill-star">&nbsp;</span><span
                                        class="fill-star">&nbsp;</span><span
                                        class="fill-star">&nbsp;</span><span>&nbsp;</span>
                            </div>
                            <p class="reviews">2 отзыва</p>
                        </div>
                        <?php
                        if ($respond->comment): ?>
                            <p class="response-message">
                                <?= Html::encode($respond->comment); ?>
                            </p>
                        <?php
                        endif; ?>
                    </div>
                    <div class="feedback-wrapper">
                        <p class="info-text"><span
                                    class="current-time"><?= Yii::$app->formatter->asRelativeTime(
                                        $respond->created_at,
                                    ); ?></span>
                        </p>
                        <?php
                        if ($respond->price): ?>
                            <p class="price price--small"><?= $respond->price; ?>
                                ₽</p>
                        <?php
                        endif; ?>
                    </div>
                    <?php
                    if ($taskHelper->status === Task::STATUS_NEW && $task->author_id === $user->id && !$respond->rejected): ?>
                        <div class="button-popup">
                            <?= Html::a(
                                'Принять',
                                ['tasks/start', 'taskId' => $task->id, 'executorId' => $respond->executor_id],
                                [
                                    'class' => 'button button--blue button--small',
                                ],
                            ); ?>
                            <?= Html::a(
                                'Отказать',
                                ['tasks/reject', 'taskId' => $task->id, 'respondId' => $respond->id],
                                [
                                    'class' => 'button button--orange button--small',
                                ],
                            ); ?>
                        </div>
                    <?php
                    endif; ?>
                </div>
            <?php
            endforeach; ?>
        <?php
        endif; ?>
    </div>
    <div class="right-column">
        <div class="right-card black info-card">
            <h4 class="head-card">Информация о задании</h4>
            <dl class="black-list">
                <dt>Категория</dt>
                <dd><?= $task->category->name; ?></dd>
                <dt>Дата публикации</dt>
                <dd><?= Yii::$app->formatter->asRelativeTime(
                    $task->created_at,
                ); ?></dd>
                <dt>Срок выполнения</dt>
                <dd><?= Yii::$app->formatter->asDatetime(
                    $task->expire_date,
                    'php:d.m.Y, H:i',
                ); ?></dd>
                <dt>Статус</dt>
                <dd><?= $task->displayStatus() ?></dd>
            </dl>
        </div>
        <?php
        if (!empty($taskFiles)): ?>
            <div class="right-card white file-card">
                <h4 class="head-card">Файлы задания</h4>
                <ul class="enumeration-list">
                    <?php
                foreach ($taskFiles as $file): ?>
                        <?php
                    if (file_exists(
                        Yii::getAlias('@webroot/') . $file->file->url,
                    )
                    ): ?>
                            <li class="enumeration-item">
                                <?= Html::a(
                                    $file->file->name,
                                    Url::to($file->file->url),
                                    [
                                        'target' => '_blank',
                                        'class' => 'link link--block link--clip',
                                    ],
                                ); ?>
                                <p class="file-size"><?= Yii::$app->formatter->asShortSize(
                                    filesize(
                                        Yii::getAlias('@webroot/')
                                            . $file->file->url,
                                    ),
                                ); ?></p>
                            </li>
                        <?php
                    endif; ?>
                    <?php
                endforeach; ?>
                </ul>
            </div>
        <?php
        endif; ?>
    </div>
</main>
<section class="pop-up pop-up--cancel pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Отмена задания.</h4>
        <p class="pop-up-text">
            <b>Внимание!</b><br>
            Вы собираетесь отменить задание.<br>
        </p>
        <?= Html::a(
            'Отменить',
            ['tasks/cancel', 'taskId' => $task->id],
            [
                'class' => 'button button--pop-up button--pink',
            ],
        ); ?>
        <div class="button-container">
            <?= Html::button('Закрыть окно', ['class' => 'button--close']) ?>
        </div>
    </div>
</section>
<section class="pop-up pop-up--refusal pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Отказ от задания</h4>
        <p class="pop-up-text">
            <b>Внимание!</b><br>
            Вы собираетесь отказаться от выполнения этого задания.<br>
            Это действие плохо скажется на вашем рейтинге и увеличит счетчик
            проваленных заданий.
        </p>
        <?= Html::a(
            'Отказаться',
            ['tasks/refuse', 'task' => $task, 'user' => $user],
            [
                'class' => 'button button--pop-up button--orange',
            ],
        ); ?>
        <div class="button-container">
            <?= Html::button('Закрыть окно', ['class' => 'button--close']) ?>
        </div>
    </div>
</section>
<section class="pop-up pop-up--completion pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Завершение задания</h4>
        <p class="pop-up-text">
            Вы собираетесь отметить это задание как выполненное.
            Пожалуйста, оставьте отзыв об исполнителе и отметьте отдельно, если
            возникли проблемы.
        </p>
        <div class="completion-form pop-up--form regular-form">
            <?php $form = ActiveForm::begin(
                ['enableAjaxValidation' => true, 'method' => 'post', 'action' => ['tasks/finish', 'taskId' => $task->id]],
            ); ?>
            <?= $form->field($review, 'comment')
                ->textarea(
                    ['labelOptions' => ['class' => 'control-label']],
                )->label('Ваш комментарий') ?>
            <?= $form->field($review, 'rating')
                ->hiddenInput(['id' => 'review-score'])
                ->label('Оценка работы') ?>

            <?= $form->field($review, 'rating')->widget(StarRating::class, [
                'pluginOptions' => [
                    'size' => 'sm',
                    'stars' => 5,
                    'min' => 0,
                    'max' => 5,
                    'step' => 1,
                    'showClear' => false,
                    'showCaption' => false,
                ],
            ]); ?>
            <?= Html::submitInput(
                'Завершить',
                ['class' => 'button button--pop-up button--blue'],
            ); ?>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<section class="pop-up pop-up--act_response pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Добавление отклика к заданию</h4>
        <p class="pop-up-text">
            Вы собираетесь оставить свой отклик к этому заданию.
            Пожалуйста, укажите стоимость работы и добавьте комментарий, если
            необходимо.
        </p>
        <div class="addition-form pop-up--form regular-form">
            <?php $form = ActiveForm::begin(
                ['enableAjaxValidation' => true, 'method' => 'post', 'action' => ['tasks/respond', 'taskId' => $task->id]],
            ); ?>
            <?= $form->field($respond, 'comment')
                ->textarea(
                    ['labelOptions' => ['class' => 'control-label']],
                ) ?>
            <?= $form->field($respond, 'price')
                ->textInput(
                    ['labelOptions' => ['class' => 'control-label']],
                ) ?>
            <?= Html::submitInput(
                'Завершить',
                ['class' => 'button button--pop-up button--blue'],
            ); ?>
            <?php ActiveForm::end(); ?>                                                          
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<div class="overlay"></div>

