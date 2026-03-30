<?php

namespace app\models;

use DateInterval;
use yii\base\Model;

class TasksForm extends Model
{
    public $categories = [];
    public $noResponds = false;
    public $period = '';

    public function attributeLabels(): array
    {
        return [
            'categories' => 'Категории',
            'noResponds' => 'Без исполнителя',
            'period' => 'Период',
        ];
    }

    public function rules(): array
    {
        return [
            ['categories', 'each', 'rule' => ['integer']],
            ['noResponds', 'boolean'],
            ['period', 'validateIsoDuration'],
        ];
    }

    public function validateIsoDuration($attribute)
    {
        if (!empty($this->$attribute)) {
            try {
                new DateInterval($this->$attribute);
            } catch (\Exception $e) {
                $this->addError($attribute, 'Выберите период из списка.');
            }
        }
    }

}
