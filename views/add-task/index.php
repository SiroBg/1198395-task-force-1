<?php
/**
 * @var \app\models\Task     $task       ;
 * @var \app\models\Category $categories ;
 *
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=' . Yii::$app->params['yandexJsApiKey'] . '&suggest_apikey=' . $Yii::$app->params['yandexSuggestApiKey'], ['position' => \yii\web\View::POS_END,]);
$this->registerJsFile('@webroot/js/yandexAutocomplete.js', ['position' => \yii\web\View::POS_END,]);
?>
<main class="main-content main-content--center container">
    <div class="add-task-form regular-form">
        <?php
        $form = ActiveForm::begin(
            [
                'enableAjaxValidation' => true,
                'method' => 'post',
            ],
        ); ?>
        <h3 class="head-main head-main">Публикация нового задания</h3>
        <?= $form->field($task, 'name')
            ->textInput(
                ['labelOptions' => ['class' => 'control-label']],
            ) ?>
        <?= $form->field($task, 'description')
            ->textarea(
                ['labelOptions' => ['class' => 'control-label']],
            ) ?>
        <?= $form->field($task, 'category_id')->dropDownList(
            ArrayHelper::map($categories, 'id', 'name'),
        ) ?>
        <?= $form->field($task, 'location')->textInput(
            [
            'labelOptions' => ['class' => 'control-label'],
            'class' => 'location-icon',
            'id' => 'suggest',
            ],
        ) ?>
        <div class="half-wrapper">
            <?= $form->field($task, 'budget')
        ->textInput(
            [
                'class' => 'budget-icon',
                'labelOptions' => ['class' => 'control-label'],
            ],
        ) ?>
            <?= $form->field($task, 'expire_date')
        ->input(
            'date',
            ['labelOptions' => ['class' => 'control-label']],
        ) ?>
        </div>
        <p class="form-label">Файлы</p>
        <?= $form->field(
            $task,
            'task_files[]',
            ['template' => '<label for="task-task_files"><div class="new-file">Добавить новый файл</div>{input}{error}</label>'],
        )
        ->fileInput([
                'multiple' => true,
                'style' => 'display:none',
            ]) ?>
        <?= Html::submitInput(
            'Опубликовать',
            ['class' => 'button button--blue'],
        ) ?>

        <?php
        ActiveForm::end(); ?>
    </div>
</main>
