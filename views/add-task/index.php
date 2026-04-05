<?php
/**
 * @var $task       ;
 * @var $categories ;
 *
 */

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

?>
<main class="main-content main-content--center container">
    <div class="add-task-form regular-form">
        <?php
        $form = ActiveForm::begin(
            [
                'enableAjaxValidation' => true,
                'method'               => 'post'
            ]
        ); ?>
        <h3 class="head-main head-main">Публикация нового задания</h3>
        <?= $form->field($task, 'name')
            ->textInput(['labelOptions' => ['class' => 'control-label']]
            ) ?>
        <?= $form->field($task, 'description')
            ->textarea(['labelOptions' => ['class' => 'control-label']]
            ) ?>
        <?= $form->field($task, 'category_id')->dropDownList(
            ArrayHelper::map($categories, 'id', 'name')
        ) ?>
        <?= $form->field($task, 'location')->textInput(
            [
                'labelOptions' => ['class' => 'control-label'],
                'class'        => 'location-icon'
            ]
        ) ?>
        <div class="half-wrapper">
            <?= $form->field($task, 'budget')
                ->textInput(
                    [
                        'class'        => 'budget-icon',
                        'labelOptions' => ['class' => 'control-label']
                    ]
                ) ?>
            <?= $form->field($task, 'expire_date')
                ->input('date', ['labelOptions' => ['class' => 'control-label']]
                ) ?>
        </div>
        <p class="form-label">Файлы</p>
        <?= $form->field(
            $task,
            'task_files[]',
            ['template' => '<label for="tasks-task_files"><div class="new-file">Добавить новый файл</div>{input}{error}</label>']
        )
            ->fileInput([
                'multiple' => true,
                'style'    => 'display:none'
            ]) ?>
        <input type="submit" class="button button--blue"
               value="Опубликовать">
        <?php
        ActiveForm::end(); ?>
    </div>
</main>
