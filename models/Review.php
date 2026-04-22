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
 * @property User        $author
 * @property User        $executor
 * @property Task        $task
 */
class Review extends \yii\db\ActiveRecord
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
                'targetClass'     => Task::class,
                'targetAttribute' => ['task_id' => 'id']
            ],
            [
                ['author_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => User::class,
                'targetAttribute' => ['author_id' => 'id']
            ],
            [
                ['executor_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => User::class,
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
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

}
