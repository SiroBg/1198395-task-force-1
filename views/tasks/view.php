<?php

use app\models\Task;
use kartik\rating\StarRating;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var \app\models\Task     $task        ;
 * @var \app\models\Respond  $responds    ;
 * @var \app\models\TaskFile $taskFiles   ;
 * @var \app\models\User     $user        ;
 * @var \app\models\Review   $reviewForm  ;
 * @var \app\models\Respond  $respondForm ;
 * @var bool                 $hasResponds ;
 */

\app\assets\ViewTaskAsset::register($this);
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
            $task->getActions() as $action
        ): ?>
            <?php
            if ($action->checkRights(
                    $task->executor_id,
                    $task->author_id,
                    $user->id,
                    $user->is_executor
                )
                && $action->getName() !== 'action_start'
                && ! $hasResponds
            ): ?>
                <?= Html::a(
                    $action->getDescription(),
                    options: [
                        'class'       => 'button button--'
                                         .$action->getButtonColor()
                                         .' action-btn',
                        'data-action' => $action->getName(),
                    ],
                ); ?>
            <?php
            endif; ?>
        <?php
        endforeach; ?>
        <?php
        if ($task->location && $task->city) : ?>
            <div class="task-map">
                <div id="map" style="width: 725px; height: 346px"
                     data-long="<?= $task->long; ?>"
                     data-lat="<?= $task->lat; ?>"></div>
                <p class="map-address town"><?= $task->city->name; ?></p>
                <p class="map-address"><?= Html::encode(
                        $task->location,
                    ); ?></p>
            </div>
        <?php
        endif; ?>
        <?php
        if ( ! empty($responds)): ?>
            <h4 class="head-regular">
                <?= $user->is_executor ? 'Ваш отклик' : 'Отклики на задание'; ?>
            </h4>
            <?php
            foreach ($responds as $respond): ?>
                <div class="response-card">
                    <img class="customer-photo"
                         src="<?= $respond->executor->profileImgFile
                             ? $respond->executor->profileImgFile->url
                             : '/img/avatar-placeholder.png' ?>"
                         width="146"
                         height="156" alt="Фото заказчика">
                    <div class="feedback-wrapper">
                        <a href="<?= Url::toRoute(
                            ['users/view', 'id' => $respond->executor->id],
                        ); ?>"
                           class="link link--block link--big"><?= Html::encode(
                                $respond->executor->name,
                            ); ?></a>
                        <div class="response-wrapper">
                            <div>
                                <?= StarRating::widget([
                                    'name'          => 'display_rating',
                                    'value'         => $respond->executor->rating,
                                    'pluginOptions' => [
                                        'displayOnly' => true,
                                        'disabled'    => true,
                                        'size'        => 'sm',
                                        'showClear'   => false,
                                        'showCaption' => false,
                                    ],
                                ]); ?>
                            </div>
                            <p class="reviews">Отзывов: <?= count(
                                    $respond->executor->reviewsAsExecutor
                                ) ?></p>
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
                    if ($task->status === Task::STATUS_NEW
                        && $task->author_id === $user->id
                        && ! $respond->rejected
                    ): ?>
                        <div class="button-popup">
                            <?= Html::a(
                                'Принять',
                                [
                                    'tasks/start',
                                    'taskId'     => $task->id,
                                    'executorId' => $respond->executor_id,
                                ],
                                [
                                    'class' => 'button button--blue button--small',
                                ],
                            ); ?>
                            <?= Html::a(
                                'Отказать',
                                [
                                    'tasks/reject',
                                    'taskId'    => $task->id,
                                    'respondId' => $respond->id,
                                ],
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
        if ( ! empty($taskFiles)): ?>
            <div class="right-card white file-card">
                <h4 class="head-card">Файлы задания</h4>
                <ul class="enumeration-list">
                    <?php
                    foreach ($taskFiles as $file): ?>
                        <?php
                        if (file_exists(
                            Yii::getAlias('@webroot/').$file->file->url,
                        )
                        ): ?>
                            <li class="enumeration-item">
                                <?= Html::a(
                                    $file->file->name,
                                    Url::to($file->file->url),
                                    [
                                        'target' => '_blank',
                                        'class'  => 'link link--block link--clip',
                                    ],
                                ); ?>
                                <p class="file-size"><?= Yii::$app->formatter->asShortSize(
                                        filesize(
                                            Yii::getAlias('@webroot/')
                                            .$file->file->url,
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
            ['tasks/refuse', 'taskId' => $task->id],
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
            <?php
            $form = ActiveForm::begin(
                [
                    'method' => 'post',
                    'action' => [
                        'tasks/finish',
                        'taskId' => $task->id,
                    ],
                ],
            ); ?>
            <?= $form->field($reviewForm, 'comment')
                     ->textarea(
                         ['labelOptions' => ['class' => 'control-label']],
                     ); ?>
            <p class="completion-head control-label">Оценка работы</p>
            <?= $form->field($reviewForm, 'rating')->widget(StarRating::class, [
                'pluginOptions' => [
                    'size'        => 'sm',
                    'stars'       => 5,
                    'step'        => 1,
                    'showClear'   => false,
                    'showCaption' => false,
                ],

            ])->label(false); ?>
            <?= Html::submitInput(
                'Завершить',
                ['class' => 'button button--pop-up button--blue'],
            ); ?>
            <?php
            ActiveForm::end(); ?>
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
            <?php
            $form = ActiveForm::begin(
                [
                    'method' => 'post',
                    'action' => [
                        'tasks/respond',
                        'taskId' => $task->id,
                    ],
                ],
            ); ?>
            <?= $form->field($respondForm, 'comment')
                     ->textarea(
                         ['labelOptions' => ['class' => 'control-label']],
                     ) ?>
            <?= $form->field($respondForm, 'price')
                     ->textInput(
                         ['labelOptions' => ['class' => 'control-label']],
                     ) ?>
            <?= Html::submitInput(
                'Завершить',
                ['class' => 'button button--pop-up button--blue'],
            ); ?>
            <?php
            ActiveForm::end(); ?>
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<div class="overlay"></div>

