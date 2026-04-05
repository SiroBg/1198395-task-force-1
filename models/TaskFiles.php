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
 * @property Files       $file
 * @property Tasks       $task
 */
class TaskFiles extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['task_id', 'file_id'], 'required'],
            [['task_id', 'file_id'], 'integer'],
            [
                ['task_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Tasks::class,
                'targetAttribute' => ['task_id' => 'id']
            ],
            [
                ['file_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Files::class,
                'targetAttribute' => ['file_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
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
    public function getFile()
    {
        return $this->hasOne(Files::class, ['id' => 'file_id']);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Tasks::class, ['id' => 'task_id']);
    }
    
}
