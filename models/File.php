<?php

namespace app\models;

/**
 * This is the model class for table "files".
 *
 * @property int         $id
 * @property string|null $created_at
 * @property string      $url
 * @property string      $file_path
 * @property string      $name
 *
 * @property TaskFile[]  $taskFiles
 * @property User[]      $users
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at'], 'safe'],
            [['url', 'file_path', 'name'], 'required'],
            [['url', 'file_path', 'name'], 'string', 'max' => 256],
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
            'url'        => 'Url',
            'file_path'  => 'File Path',
            'name'       => 'Name',
        ];
    }

    /**
     * Gets query for [[TaskFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskFiles(): \yii\db\ActiveQuery
    {
        return $this->hasMany(TaskFile::class, ['file_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(User::class, ['profile_img_file_id' => 'id']);
    }

    public function getId(): int
    {
        return $this->id;
    }

}
