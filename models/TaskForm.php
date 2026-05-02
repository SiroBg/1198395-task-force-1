<?php

namespace app\models;

use DateInterval;
use yii\base\Model;

class TaskForm extends Model
{
    public array|string $categories = [];
    public bool         $noResponds = false;
    public bool         $remoteTask = false;
    public string       $period     = '';

    public const array PERIODS_OPTIONS
        = [
            ''      => 'Любой',
            'PT1H'  => '1 час',
            'PT12H' => '12 часов',
            'PT24H' => '24 часа',
        ];

    public function attributeLabels(): array
    {
        return [
            'categories' => 'Категории',
            'noResponds' => 'Без исполнителя',
            'remoteTask' => 'Удалённая работа',
            'period'     => 'Период',
        ];
    }

    public function rules(): array
    {
        return [
            ['categories', 'each', 'rule' => ['integer']],
            [['noResponds', 'remoteTask'], 'boolean'],
            ['period', 'validateIsoDuration'],
        ];
    }

    public function validateIsoDuration($attribute): void
    {
        if ( ! empty($this->$attribute)) {
            try {
                new DateInterval($this->$attribute);
            } catch (\Exception $e) {
                $this->addError($attribute, 'Выберите период из списка.');
            }
        }
    }

}
