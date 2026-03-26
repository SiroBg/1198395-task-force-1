<?php

/**
 * @var $tasks
 * @var $categories
 * @var $tasksFrom
*/
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main head-task">Новые задания</h3>
        <?php foreach ($tasks as $task) : ?>
            <div class="task-card">
            <div class="header-task">
                <a href="#" class="link link--block link--big"><?= htmlspecialchars($task->name); ?></a>
                <p class="price price--task"><?= $task->budget; ?> ₽</p>
            </div>
            <p class="info-text"><span class="current-time">
                <?= Yii::$app->formatter->asRelativeTime($task->created_at); ?>
            </span>
            </p>
            <p class="task-text"><?= htmlspecialchars($task->description); ?></p>
            <div class="footer-task">
                <p class="info-text town-text"><?= $task->city->name; ?></p>
                <p class="info-text category-text"><?= $task->category->name; ?></p>
                <a href="#" class="button button--black">Смотреть Задание</a>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="pagination-wrapper">
            <ul class="pagination-list">
                <li class="pagination-item mark">
                    <a href="#" class="link link--page"></a>
                </li>
                <li class="pagination-item">
                    <a href="#" class="link link--page">1</a>
                </li>
                <li class="pagination-item pagination-item--active">
                    <a href="#" class="link link--page">2</a>
                </li>
                <li class="pagination-item">
                    <a href="#" class="link link--page">3</a>
                </li>
                <li class="pagination-item mark">
                    <a href="#" class="link link--page"></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="right-column">
        <div class="right-card black">
            <div class="search-form">
                <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['tasks/index'],
            ]); ?>

            <h4 class="head-card">Категории</h4>
            <div class="form-group">
                <div class="checkbox-wrapper">

                <?= $form->field($tasksForm, 'categories')->checkboxList(
                    ArrayHelper::map($categories, 'id', 'name'),
                    [
                        'separator' => '<br>',
                        'class' => 'control-label',
                    ],
                )->error(['tag' => false])->label(false); ?>
            </div>
            </div>
            <h4 class="head-card">Дополнительно</h4>
            <div class="form-group">
                <?= $form->field($tasksForm, 'noResponds')->checkbox([
            'labelOptions' => ['class' => 'control-label'],
            ])->error(['tag' => false]); ?>
            </div>
            <h4 class="head-card">Период</h4>
            <div class="form-group">

            </div>
            <input type="submit" class="button button--blue" value="Искать">

            <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</main>
