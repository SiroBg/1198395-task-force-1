<?php

use kartik\rating\StarRating;

/**
 * @var \app\models\User     $user        ;
 * @var \app\models\Review[] $userReviews ;
 */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main"><?= Html::encode($user->name); ?></h3>
        <div class="user-card">
            <div class="photo-rate">
                <?= Html::img(
                    Html::encode(
                        $user->profileImgFile->url ??
                        '/img/avatar-placeholder.png'
                    ),
                    [
                        'class' => 'card-photo',
                        'style' => 'width: 191px; height: 190px;'
                    ]
                ); ?>
                <div class="card-rate">
                    <?= StarRating::widget([
                        'name'          => 'display_rating',
                        'value'         => $user->rating,
                        'pluginOptions' => [
                            'displayOnly' => true,
                            'disabled'    => true,
                            'size'        => 'sm',
                            'showClear'   => false,
                            'showCaption' => false,
                        ],
                    ]); ?>
                    <span class="current-rate"><?= $user->rating ?></span>
                </div>
            </div>
            <p class="user-description"><?= Html::encode(
                    $user->about ?? ''
                ); ?></p>
        </div>
        <div class="specialization-bio">
            <div class="specialization">
                <p class="head-info">Специализации</p>
                <ul class="special-list">
                    <?php
                    foreach ($user->userCategories as $userCategory): ?>
                        <li class="special-item">
                            <a href="<?= Url::toRoute(
                                [
                                    'tasks/index',
                                    'TaskForm[categories][]' => $userCategory->category->id
                                ]
                            ); ?>"
                               class="link link--regular"><?= $userCategory->category->name; ?></a>
                        </li>
                    <?php
                    endforeach; ?>
                </ul>
            </div>
            <div class="bio">
                <p class="head-info">Био</p>
                <p class="bio-info"><span class="country-info">Россия</span>,
                    <span class="town-info"><?= $user->city->name; ?></span>,
                    <?php
                    if ($user->birthday): ?>
                        <span class="age-info">День рождения: <?= Yii::$app->formatter->asDate(
                                $user->birthday,
                                'php:d-m-Y'
                            ); ?></span>
                    <?php
                    endif; ?>
                </p>
            </div>
        </div>
        <?php
        if ( ! empty($userReviews)): ?>
            <h4 class="head-regular">Отзывы заказчиков</h4>
            <?php
            foreach ($userReviews as $review): ?>
                <div class="response-card">
                    <img class="customer-photo"
                         src="<?= $review->author->profileImgFile->url ?? '/img/avatar-placeholder.png' ?>" width="120"
                         height="127" alt="Фото заказчика">
                    <div class="feedback-wrapper">
                        <p class="feedback">
                            <?= Html::encode($review->comment) ?>
                        </p>
                        <p class="task">Задание «<?= Html::a(
                                Html::encode($review->task->name),
                                '/tasks/view'.$review->task->id,
                                ['class' => 'link link--small']
                            ) ?>» выполнено</p>
                    </div>
                    <div class="feedback-wrapper">
                        <?= StarRating::widget([
                            'name'          => 'display_rating',
                            'value'         => $review->rating,
                            'pluginOptions' => [
                                'displayOnly' => true,
                                'disabled'    => true,
                                'size'        => 'sm',
                                'showClear'   => false,
                                'showCaption' => false,
                            ],
                        ]); ?>
                        <p class="info-text"><span class="current-time"><?= Yii::$app->formatter->asRelativeTime(
                                    $review->created_at
                                ) ?></span>
                        </p>
                    </div>
                </div>
            <?php
            endforeach; ?>
        <?php
        endif; ?>
    </div>
    <div class="right-column">
        <div class="right-card black">
            <h4 class="head-card">Статистика исполнителя</h4>
            <dl class="black-list">
                <dt>Всего заказов</dt>
                <dd><?= $user->finishedTasksAmount ?> выполнено, <?= $user->failedTasksAmount ?> провалено</dd>
                <dt>Место в рейтинге</dt>
                <dd><?= $user->ratingPlacement ?> место</dd>
                <dt>Дата регистрации</dt>
                <dd><?= Yii::$app->formatter->asDatetime(
                        $user->created_at,
                        'php:d.m.Y, H:i'
                    ); ?></dd>
                <dt>Статус</dt>
                <dd><?= $user->isBusy ? 'Занят заказом' : 'Открыт для новых заказов' ?></dd>
            </dl>
        </div>
        <?php
        if ($user->show_contacts): ?>
            <div class="right-card white">
                <h4 class="head-card">Контакты</h4>
                <ul class="enumeration-list">
                    <?php
                    if ($user->phone): ?>
                        <li class="enumeration-item">
                            <?= Html::a(
                                Html::encode($user->phone),
                                'tel+:'.Html::encode($user->phone),
                                ['class' => 'link link--block link--phone']
                            ); ?>
                        </li>
                    <?php
                    endif; ?>
                    <?php
                    if ($user->email): ?>
                        <li class="enumeration-item">
                            <?= Html::a(
                                Html::encode($user->email),
                                'mailto:'.Html::encode($user->email),
                                ['class' => 'link link--block link--email']
                            ); ?>
                        </li>
                    <?php
                    endif; ?>
                    <?php
                    if ($user->telegram): ?>
                        <li class="enumeration-item">
                            <?= Html::a(
                                Html::encode($user->telegram),
                                'https://t.me'.Html::encode($user->telegram),
                                ['class' => 'link link--block link--tg', 'target' => '_blank']
                            ); ?>
                        </li>
                    <?php
                    endif; ?>
                </ul>
            </div>
        <?php
        endif; ?>
    </div>
</main>
