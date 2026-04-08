<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "files".
 *
 * @property int         $id
 * @property string|null $created_at
 * @property string      $url
 * @property string      $file_path
 *
 * @property TaskFiles[] $taskFiles
 * @property Users[]     $users
 */
class Files extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['url', 'file_path'], 'required'],
            [['url', 'file_path'], 'string', 'max' => 256],
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
            'url'        => 'Url',
            'file_path'  => 'File Path',
        ];
    }

    /**
     * Gets query for [[TaskFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskFiles()
    {
        return $this->hasMany(TaskFiles::class, ['file_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::class, ['profile_img_file_id' => 'id']);
    }

    public function getId(): int
    {
        return $this->id;
    }

}
