<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task_files".
 *
 * @property int         $id
 * @property string|null $created_at
 * @property int         $task_id
 * @property int         $file_id
 *
 * @property File        $file
 * @property Task        $task
 */
class TaskFile extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'task_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at'], 'safe'],
            [['task_id', 'file_id'], 'required'],
            [['task_id', 'file_id'], 'integer'],
            [
                ['task_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Task::class,
                'targetAttribute' => ['task_id' => 'id']
            ],
            [
                ['file_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => File::class,
                'targetAttribute' => ['file_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'created_at' => 'Created At',
            'task_id'    => 'Task ID',
            'file_id'    => 'File ID',
        ];
    }

    /**
     * Gets query for [[File]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(File::class, ['id' => 'file_id']);
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
