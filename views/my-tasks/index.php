<?php

use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * @var ActiveDataProvider $provider ;
 * @var \app\models\User   $user     ;
 * @var string             $title    ;
 * @var string             $status   ;
 */
?>
<main class="main-content container">
    <div class="left-menu">
        <h3 class="head-main head-task">Мои задания</h3>
        <ul class="side-menu-list">
            <?php
            if (!$user->is_executor) : ?>
                <li class="side-menu-item <?= $status === 'new' ? 'side-menu-item--active' : '' ?>">
                    <a class="link link--nav" href="<?= Url::to(['my-tasks/index', 'status' => 'new']) ?>">
                        Новые
                    </a>
                </li>
            <?php
            endif; ?>
            <li class="side-menu-item">
                <li class="side-menu-item <?= $status === 'active' ? 'side-menu-item--active' : '' ?>">
                    <a class="link link--nav" href="<?= Url::to(['my-tasks/index', 'status' => 'active']) ?>">
                        В процессе
                    </a>
                </li>
            </li>
            <?php
            if ($user->is_executor) : ?>
                <li class="side-menu-item <?= $status === 'expired' ? 'side-menu-item--active' : '' ?>">
                    <a class="link link--nav" href="<?= Url::to(['my-tasks/index', 'status' => 'expired']) ?>">
                        Просрочено
                    </a>
                </li>
            <?php
            endif; ?>
            <li class="side-menu-item <?= $status === 'closed' ? 'side-menu-item--active' : '' ?>">
                <a class="link link--nav" href="<?= Url::to(['my-tasks/index', 'status' => 'closed']) ?>">
                    Закрытые
                </a>
            </li>
        </ul>
    </div>
    <div class="left-column left-column--task">
        <h3 class="head-main head-regular"><?= $title ; ?></h3>
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