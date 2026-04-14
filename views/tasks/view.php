<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \app\models\Tasks    $taskModel ;
 * @var TaskForce\Task       $task      ;
 * @var \app\models\Responds $responds  ;
 * @var array                $user      ;
 */

?>

<main class="main-content container">
    <div class="left-column">
        <div class="head-wrapper">
            <h3 class="head-main"><?= Html::encode($taskModel->name); ?></h3>
            <?php
            if ($taskModel->budget): ?>
                <p class="price price--big"><?= Html::encode(
                        $taskModel->budget
                    ); ?>
                    ₽</p>
            <?php
            endif; ?>
        </div>
        <p class="task-description"><?= Html::encode(
                $taskModel->description,
            ); ?></p>
        <?php
        foreach (
            $task->getActions(
                $user->id,
                $user->is_executor,
            ) as $action
        ): ?>
            <?= Html::a(
                $action->getDescription(),
                options: [
                    'class'       => 'button button--'
                        . $action->getButtonColor()
                        . ' action-btn',
                    'data-action' => $action->getName(),
                ]
            ); ?>
        <?php
        endforeach; ?>
        <?php
        if ($taskModel->location && $taskModel->city) : ?>
            <div class="task-map">
                <img class="map" src="img/map.png" width="725" height="346"
                     alt="<?= Html::encode($taskModel->location); ?>">
                <p class="map-address town"><?= $taskModel->city->name; ?></p>
                <p class="map-address"><?= Html::encode(
                        $taskModel->location
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
                    if ($taskModel->author_id === $user->id): ?>
                        <div class="button-popup">
                            <a href="#"
                               class="button button--blue button--small">Принять</a>
                            <a href="#"
                               class="button button--orange button--small">Отказать</a>
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
                <dd><?= $taskModel->category->name; ?></dd>
                <dt>Дата публикации</dt>
                <dd><?= Yii::$app->formatter->asRelativeTime(
                        $taskModel->created_at,
                    ); ?></dd>
                <dt>Срок выполнения</dt>
                <dd><?= Yii::$app->formatter->asDatetime(
                        $taskModel->expire_date,
                        'php:d.m.Y, H:i',
                    ); ?></dd>
                <dt>Статус</dt>
                <dd><?= $taskModel->displayStatus() ?></dd>
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
                                        'class'  => 'link link--block link--clip',
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
            ['tasks/refuse', 'taskId' => $taskModel->id],
            [
                'class' => 'button button--pop-up button--orange',
            ]
        ); ?>
        <a class="button button--pop-up button--orange">Отказаться</a>
        <div class="button-container">
            <?= Html::button('Закрыть окно', ['class' => 'button--close']) ?>
            <button class="button--close" type="button">Закрыть окно</button>
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
            <form>
                <div class="form-group">
                    <label class="control-label" for="completion-comment">Ваш
                        комментарий</label>
                    <textarea id="completion-comment"></textarea>
                </div>
                <p class="completion-head control-label">Оценка работы</p>
                <div class="stars-rating big active-stars">
                    <span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span>
                </div>
                <input type="submit" class="button button--pop-up button--blue"
                       value="Завершить">
            </form>
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
            <form>
                <div class="form-group">
                    <label class="control-label" for="addition-comment">Ваш
                        комментарий</label>
                    <textarea id="addition-comment"></textarea>
                </div>
                <div class="form-group">
                    <label class="control-label"
                           for="addition-price">Стоимость</label>
                    <input id="addition-price" type="text">
                </div>
                <input type="submit" class="button button--pop-up button--blue"
                       value="Завершить">
            </form>
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<div class="overlay"></div>

