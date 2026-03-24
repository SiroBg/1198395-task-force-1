<?php

/** @var $tasks */

?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main head-task">Новые задания</h3>
        <?php foreach ($tasks as $task) : ?>
            <div class="task-card">
            <div class="header-task">
                <a href="#" class="link link--block link--big"><?= $task['name']; ?></a>
                <p class="price price--task"><?= $task['budget']; ?> ₽</p>
            </div>
            <p class="info-text"><span class="current-time">4 часа </span>назад
            </p>
            <p class="task-text"><?= $task['description']; ?></p>
            <div class="footer-task">
                <p class="info-text town-text"><?= $task->city['name']; ?></p>
                <p class="info-text category-text"><?= $task->category['name']; ?></p>
                <a href="#" class="button button--black">Смотреть Задание</a>
            </div>
        </div>
        <?php endforeach; ?>
        
    </div>
</main>
