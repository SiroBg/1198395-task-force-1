<?php

namespace app\models;

/**
 * This is the model class for table "responds".
 *
 * @property int         $id
 * @property string|null $created_at
 * @property int         $task_id
 * @property int         $executor_id
 * @property string|null $comment
 * @property int|null    $price
 * @property int         $rejected
 *
 * @property Users       $executor
 * @property Tasks       $task
 */
class Responds extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'responds';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['comment', 'price'], 'default', 'value' => null],
            [['rejected'], 'default', 'value' => 0],
            [['created_at'], 'safe'],
            [['task_id', 'executor_id'], 'required'],
            [['task_id', 'executor_id', 'price', 'rejected'], 'integer'],
            [['comment'], 'string'],
            [
                ['task_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Tasks::class,
                'targetAttribute' => ['task_id' => 'id'],
            ],
            [
                ['executor_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Users::class,
                'targetAttribute' => ['executor_id' => 'id'],
            ],
            [
                ['executor_id', 'task_id'], 'unique', 'targetAttribute' => ['executor_id', 'task_id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'task_id' => 'Task ID',
            'executor_id' => 'Executor ID',
            'comment' => 'Ваш комментарий',
            'price' => 'Стоимость',
            'rejected' => 'Rejected',
        ];
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Tasks::class, ['id' => 'task_id']);
    }

}
