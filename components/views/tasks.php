<?php
/**
 * @var \yii\data\ActiveDataProvider $provider ;
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

?>
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
        'pagination'           => $provider->pagination,
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
