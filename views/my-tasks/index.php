<?php

use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * @var ActiveDataProvider $provider ;
 * @var \app\models\User   $user     ;
 */
?>
<main class="main-content container">
    <div class="left-menu">
        <h3 class="head-main head-task">Мои задания</h3>
        <ul class="side-menu-list">
            <?php
            if (!$user->is_executor) : ?>
                <li class="side-menu-item side-menu-item--active">
                    <?= Html::a(
                        'Новые',
                        '/my-tasks?status=new',
                        ['class' => 'link link--nav']
                    ) ?>
                </li>
            <?php
            endif; ?>
            <li class="side-menu-item">
                <?= Html::a(
                    'В процессе',
                    '/my-tasks?status=active',
                    ['class' => 'link link--nav']
                ) ?>
            </li>
            <?php
            if ($user->is_executor) : ?>
                <li class="side-menu-item">
                    <?= Html::a(
                        'Просроченные',
                        '/my-tasks?status=expired',
                        ['class' => 'link link--nav']
                    ) ?>
                </li>
            <?php
            endif; ?>
            <li class="side-menu-item">
                <?= Html::a(
                    'Закрытые',
                    '/my-tasks?status=closed',
                    ['class' => 'link link--nav']
                ) ?>
            </li>
        </ul>
    </div>
    <div class="left-column left-column--task">
        <h3 class="head-main head-regular">Задания</h3>
        <?php
        foreach ($provider->getModels() as $task) : ?>
            <div class="task-card">
                <div class="header-task">
                    <a href="/tasks/view/<?= $task->id; ?>"
                       class="link link--block link--big"><?= Html::encode(
                            $task->name,
                        ); ?></a>
                    <?php
                    if ($task->budget): ?>
                        <p class="price price--task"><?= $task->budget; ?> ₽</p>
                    <?php
                    endif; ?>
                </div>
                <p class="info-text"><span class="current-time">
                <?= Yii::$app->formatter->asRelativeTime($task->created_at); ?>
            </span>
                </p>
                <p class="task-text"><?= Html::encode(
                        $task->description,
                    ); ?></p>

                <div class="footer-task">
                    <?php
                    if ($task->city) : ?>
                        <p class="info-text town-text"><?= $task->city->name; ?></p>
                    <?php
                    endif; ?>
                    <p class="info-text category-text"><?= $task->category->name; ?></p>
                    <a href="/tasks/view/<?= $task->id; ?>"
                       class="button button--black">Смотреть
                        Задание</a>
                </div>
            </div>
        <?php
        endforeach; ?>
        <?php
        if ($provider->pagination->pageCount > 1): ?>
            <?= LinkPager::widget([
                'pagination' => $provider->pagination,
                'options' => ['class' => 'pagination-list'],
                'linkContainerOptions' => ['class' => 'pagination-item'],
                'linkOptions' => ['class' => 'link link--page'],
                'activePageCssClass' => 'pagination-item--active',
                'disabledPageCssClass' => 'mark',
                'prevPageLabel' => '',
                'nextPageLabel' => '',
                'prevPageCssClass' => 'pagination-item mark',
                'nextPageCssClass' => 'pagination-item mark',
                'maxButtonCount' => 3,
            ]) ?>
        <?php
        endif; ?>
    </div>
</main>