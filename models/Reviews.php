<?php

namespace app\models;

/**
 * This is the model class for table "reviews".
 *
 * @property int         $id
 * @property string|null $created_at
 * @property int         $author_id
 * @property int         $executor_id
 * @property int         $task_id
 * @property string      $comment
 * @property int         $rating
 *
 * @property Users       $author
 * @property Users       $executor
 * @property Tasks       $task
 */
class Reviews extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'reviews';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at'], 'safe'],
            [
                ['author_id', 'executor_id', 'task_id', 'comment', 'rating'],
                'required'
            ],
            [['author_id', 'executor_id', 'task_id', 'rating'], 'integer'],
            [['comment'], 'string'],
            [
                ['task_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Tasks::class,
                'targetAttribute' => ['task_id' => 'id']
            ],
            [
                ['author_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Users::class,
                'targetAttribute' => ['author_id' => 'id']
            ],
            [
                ['executor_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Users::class,
                'targetAttribute' => ['executor_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'          => 'ID',
            'created_at'  => 'Created At',
            'author_id'   => 'Author ID',
            'executor_id' => 'Executor ID',
            'task_id'     => 'Task ID',
            'comment'     => 'Ваш комментарий',
            'rating'      => 'Оценка работы',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
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
