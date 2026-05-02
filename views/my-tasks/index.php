<?php

use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * @var ActiveDataProvider $provider ;
 * @var \app\models\User   $user     ;
 * @var string             $status   ;
 */
?>
<main class="main-content container">
    <div class="left-menu">
        <h3 class="head-main head-task">Мои задания</h3>
        <ul class="side-menu-list">
            <?php
            if ( ! $user->is_executor) : ?>
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
        <h3 class="head-main head-regular">Список заданий</h3>
        <?= \app\components\TaskWidget::widget(['provider' => $provider]) ?>
    </div>
</main>